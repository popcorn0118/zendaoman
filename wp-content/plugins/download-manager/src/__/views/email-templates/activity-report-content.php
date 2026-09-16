<?php
/**
 * Activity Report Email Content Template
 *
 * Variables available:
 * - $data: Array with report data
 * - $sections: Array of enabled sections
 *
 * Rendered into the {{report_content}} slot of the configured email wrapper, which supplies a
 * 600px table shell, the site header and the global footer. Everything here is therefore
 * table-based with inline styles only: no <style> block, no media queries, no flexbox and no
 * gradients, so the layout survives Outlook's Word rendering engine unchanged. Metric cards are
 * laid out two-up rather than four-up so they stay legible at ~340px without needing a media
 * query to stack them.
 *
 * @package WPDM
 * @since 7.0.2
 */

if (!defined('ABSPATH')) die('!');

if (!function_exists('wpdm_ar_tokens')) {

    /**
     * Design tokens for the report. Kept in one place so the palette and type scale stay
     * consistent across sections and can be themed without touching markup.
     */
    function wpdm_ar_tokens(): array
    {
        return [
            'font'        => "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif",
            'tnum'        => 'font-variant-numeric:tabular-nums;',
            'ink'         => '#0f172a',
            'ink_soft'    => '#334155',
            'muted'       => '#64748b',
            'line'        => '#e2e8f0',
            'line_soft'   => '#f1f5f9',
            'surface'     => '#ffffff',
            'surface_alt' => '#f8fafc',
            'accent'      => '#4f46e5',
            'accent_soft' => '#eef2ff',
            'pos_bg'      => '#ecfdf5',
            'pos_fg'      => '#047857',
            'neg_bg'      => '#fef2f2',
            'neg_fg'      => '#b91c1c',
            'flat_bg'     => '#f1f5f9',
            'flat_fg'     => '#475569',
            'gold_bg'     => '#fffbeb',
            'gold_fg'     => '#b45309',
        ];
    }

    /**
     * Delta pill. Direction is carried by a glyph as well as colour so the meaning survives
     * greyscale printing and colour-blind readers (WCAG 1.4.1).
     *
     * The sign on the value outranks the caller's class because the data layer reports "no
     * change" as an unsigned "0%" while still classing it positive; showing that as green
     * growth would overstate a flat period. $class only breaks ties for unsigned non-zero values.
     */
    function wpdm_ar_delta(array $t, string $change, ?string $class = null): string
    {
        $change = trim($change);
        if ($change === '') return '';

        if (strpos($change, '+') === 0) {
            $bg = $t['pos_bg']; $fg = $t['pos_fg']; $glyph = '&#9650;';
        } elseif (strpos($change, '-') === 0) {
            $bg = $t['neg_bg']; $fg = $t['neg_fg']; $glyph = '&#9660;';
        } elseif ((float) $change == 0.0) {
            $bg = $t['flat_bg']; $fg = $t['flat_fg']; $glyph = '&#8211;';
        } elseif ($class === 'negative') {
            $bg = $t['neg_bg']; $fg = $t['neg_fg']; $glyph = '&#9660;';
        } else {
            $bg = $t['pos_bg']; $fg = $t['pos_fg']; $glyph = '&#9650;';
        }

        return '<span style="display:inline-block;padding:3px 9px;background-color:' . $bg . ';color:' . $fg
             . ';font-family:' . $t['font'] . ';font-size:12px;font-weight:600;line-height:1.4;border-radius:100px;white-space:nowrap;' . $t['tnum'] . '">'
             . $glyph . '&nbsp;' . esc_html($change) . '</span>';
    }

    /**
     * Metric card. Label sits above the figure so a column of cards scans as a list of names
     * first, values second.
     */
    function wpdm_ar_stat(array $t, string $label, string $value, string $sub = ''): string
    {
        $html = '<table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:' . $t['surface_alt'] . ';border:1px solid ' . $t['line'] . ';border-radius:10px;">'
              . '<tr><td style="padding:16px 18px;font-family:' . $t['font'] . ';">'
              . '<div style="font-size:11px;font-weight:600;color:' . $t['muted'] . ';text-transform:uppercase;letter-spacing:0.06em;">' . $label . '</div>'
              . '<div style="padding-top:6px;font-size:24px;line-height:1.15;font-weight:700;color:' . $t['ink'] . ';letter-spacing:-0.02em;' . $t['tnum'] . '">' . $value . '</div>';

        if ($sub !== '') {
            $html .= '<div style="padding-top:6px;font-size:12px;line-height:1.4;color:' . $t['muted'] . ';">' . $sub . '</div>';
        }

        return $html . '</td></tr></table>';
    }

    /**
     * Lay metric cards out two-up. Each entry is [label, value, sub].
     *
     * Gutters come from cell padding because border-spacing is unsupported in Outlook, and
     * paired cards are padded to a matching line count rather than stretched with height:100%,
     * which email clients honour inconsistently. Both give a shared baseline everywhere.
     */
    function wpdm_ar_stat_row(array $t, array $cards, int $gutter = 12): string
    {
        $cards = array_values(array_filter($cards));
        $rows  = array_chunk($cards, 2);
        $half  = (int) floor($gutter / 2);
        $html  = '';

        foreach ($rows as $i => $row) {
            $has_sub = false;
            foreach ($row as $card) {
                if (trim((string) ($card[2] ?? '')) !== '') $has_sub = true;
            }

            $html .= '<table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0"' . ($i > 0 ? ' style="padding-top:' . $gutter . 'px;"' : '') . '><tr>';

            foreach ($row as $j => $card) {
                $sub = (string) ($card[2] ?? '');
                if ($sub === '' && $has_sub) $sub = '&nbsp;';

                $pad = count($row) === 1
                    ? ''
                    : ($j === 0 ? 'padding-right:' . $half . 'px;' : 'padding-left:' . $half . 'px;');

                $html .= '<td width="' . (count($row) === 1 ? '100' : '50') . '%" style="vertical-align:top;' . $pad . '">'
                       . wpdm_ar_stat($t, (string) $card[0], (string) $card[1], $sub)
                       . '</td>';
            }

            $html .= '</tr></table>';
        }

        return $html;
    }

    /**
     * Section shell: eyebrow, title and body inside a bordered surface.
     */
    function wpdm_ar_section(array $t, string $title, string $body, string $eyebrow = '', ?string $accent = null): string
    {
        $accent = $accent ?: $t['accent'];

        $head = '<table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0"><tr>'
              . '<td width="3" style="width:3px;background-color:' . $accent . ';border-radius:2px;font-size:0;line-height:0;">&nbsp;</td>'
              . '<td style="padding-left:10px;font-family:' . $t['font'] . ';">';

        if ($eyebrow !== '') {
            $head .= '<div style="font-size:10px;font-weight:600;color:' . $t['muted'] . ';text-transform:uppercase;letter-spacing:0.08em;">' . $eyebrow . '</div>';
        }

        $head .= '<div style="' . ($eyebrow !== '' ? 'padding-top:3px;' : '') . 'font-size:15px;font-weight:700;color:' . $t['ink'] . ';letter-spacing:-0.01em;">' . $title . '</div>'
               . '</td></tr></table>';

        return '<table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:' . $t['surface'] . ';border:1px solid ' . $t['line'] . ';border-radius:12px;">'
             . '<tr><td style="padding:20px 20px 8px 20px;">' . $head . '</td></tr>'
             . '<tr><td style="padding:0 20px 20px 20px;">' . $body . '</td></tr>'
             . '</table>';
    }

    /**
     * Proportion bar. Solid fill rather than a gradient, which Outlook drops entirely.
     */
    function wpdm_ar_bar(array $t, float $pct, ?string $color = null): string
    {
        $pct   = max(0, min(100, $pct));
        $color = $color ?: $t['accent'];
        $track = '<table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:' . $t['line_soft'] . ';border-radius:3px;"><tr><td height="6" style="height:6px;line-height:6px;font-size:0;">';

        if ($pct <= 0) {
            return $track . '&nbsp;</td></tr></table>';
        }

        return $track
             . '<table role="presentation" width="' . round($pct, 2) . '%" border="0" cellspacing="0" cellpadding="0" style="background-color:' . $color . ';border-radius:3px;"><tr>'
             . '<td height="6" style="height:6px;line-height:6px;font-size:0;">&nbsp;</td></tr></table>'
             . '</td></tr></table>';
    }

    /**
     * Empty state. Designed rather than left blank so a quiet period still looks intentional.
     */
    function wpdm_ar_empty(array $t, string $message): string
    {
        return '<table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:' . $t['surface_alt'] . ';border:1px solid ' . $t['line'] . ';border-radius:10px;">'
             . '<tr><td align="center" style="padding:28px 20px;font-family:' . $t['font'] . ';font-size:13px;line-height:1.6;color:' . $t['muted'] . ';">'
             . $message . '</td></tr></table>';
    }

    /**
     * Data-table column header. Uses <th scope="col"> so the table stays navigable.
     */
    function wpdm_ar_th(array $t, string $label, string $align = 'left', string $width = ''): string
    {
        return '<th scope="col" align="' . $align . '"' . ($width !== '' ? ' width="' . $width . '"' : '') . ' style="padding:0 0 8px 0;font-family:' . $t['font']
             . ';font-size:10px;font-weight:600;color:' . $t['muted'] . ';text-transform:uppercase;letter-spacing:0.08em;text-align:' . $align
             . ';border-bottom:1px solid ' . $t['line'] . ';">' . $label . '</th>';
    }

    /**
     * Shared cell styling for data-table body rows.
     */
    function wpdm_ar_td(array $t, string $align = 'left', bool $last = false): string
    {
        return 'padding:11px 0;font-family:' . $t['font'] . ';font-size:14px;line-height:1.45;color:' . $t['ink_soft']
             . ';text-align:' . $align . ';vertical-align:top;' . ($last ? '' : 'border-bottom:1px solid ' . $t['line_soft'] . ';');
    }
}

$t          = wpdm_ar_tokens();
$blocks     = [];
$link_style = 'color:' . $t['accent'] . ';text-decoration:none;font-weight:600;';

/* ---------------------------------------------------------------- Downloads */

if (!empty($data['download_summary'])) {
    $summary = $data['download_summary'];
    $delta   = wpdm_ar_delta($t, (string) $summary['change'], $summary['change_class'] ?? null);

    ob_start(); ?>
    <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td style="font-family:<?php echo $t['font']; ?>;font-size:40px;line-height:1.05;font-weight:700;color:<?php echo $t['ink']; ?>;letter-spacing:-0.03em;<?php echo $t['tnum']; ?>">
                <?php echo esc_html(number_format_i18n((int) $summary['total'])); ?>
            </td>
            <td align="right" style="vertical-align:bottom;"><?php echo $delta; ?></td>
        </tr>
        <tr>
            <td colspan="2" style="padding-top:6px;font-family:<?php echo $t['font']; ?>;font-size:13px;color:<?php echo $t['muted']; ?>;">
                <?php
                printf(
                    /* translators: %s: download count for the preceding period */
                    esc_html__('Total downloads · %s in the previous period', 'download-manager'),
                    '<span style="' . $t['tnum'] . '">' . esc_html(number_format_i18n((int) ($summary['previous'] ?? 0))) . '</span>'
                );
                ?>
            </td>
        </tr>
    </table>
    <div style="padding-top:18px;">
        <?php
        echo wpdm_ar_stat_row($t, [
            [esc_html__('Daily average', 'download-manager'), esc_html(number_format_i18n((float) $summary['daily_average'], 1)), ''],
            [esc_html__('Peak day', 'download-manager'), esc_html(number_format_i18n((int) $summary['peak_day_count'])), esc_html($summary['peak_day'])],
        ]);
        ?>
    </div>
    <?php
    $blocks[] = wpdm_ar_section($t, esc_html__('Downloads', 'download-manager'), ob_get_clean(), esc_html__('Overview', 'download-manager'));
}

/* ------------------------------------------------------------ Top packages */

if (!empty($data['top_packages'])) {
    ob_start();

    if (count($data['top_packages']) > 0) {
        $last = count($data['top_packages']) - 1; ?>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <?php
                    echo wpdm_ar_th($t, esc_html__('Package', 'download-manager'));
                    echo wpdm_ar_th($t, esc_html__('Downloads', 'download-manager'), 'right', '96');
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_values($data['top_packages']) as $i => $package):
                    $rank    = (int) $package['rank'];
                    $is_lead = $rank <= 3; ?>
                    <tr>
                        <td style="<?php echo wpdm_ar_td($t, 'left', $i === $last); ?>padding-right:12px;">
                            <table role="presentation" border="0" cellspacing="0" cellpadding="0" width="100%">
                                <tr>
                                    <td width="26" style="vertical-align:top;padding-top:1px;">
                                        <span style="display:inline-block;min-width:20px;padding:1px 5px;text-align:center;border-radius:5px;font-size:11px;font-weight:700;<?php echo $t['tnum']; ?><?php echo $is_lead
                                            ? 'background-color:' . $t['gold_bg'] . ';color:' . $t['gold_fg'] . ';'
                                            : 'background-color:' . $t['line_soft'] . ';color:' . $t['muted'] . ';'; ?>"><?php echo esc_html(number_format_i18n($rank)); ?></span>
                                    </td>
                                    <td style="vertical-align:top;">
                                        <a href="<?php echo esc_url($package['url']); ?>" style="<?php echo $link_style; ?>"><?php echo esc_html($package['title']); ?></a>
                                        <div style="padding-top:8px;"><?php echo wpdm_ar_bar($t, (float) $package['bar_width']); ?></div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="<?php echo wpdm_ar_td($t, 'right', $i === $last); ?>font-weight:700;color:<?php echo $t['ink']; ?>;<?php echo $t['tnum']; ?>">
                            <?php echo esc_html(number_format_i18n((int) $package['downloads'])); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php } else {
        echo wpdm_ar_empty($t, esc_html__('No downloads were recorded during this period.', 'download-manager'));
    }

    $blocks[] = wpdm_ar_section($t, esc_html__('Top downloads', 'download-manager'), ob_get_clean(), esc_html__('Most active', 'download-manager'));
}

/* --------------------------------------------------------------- Trending */

if (!empty($data['trending_packages'])) {
    ob_start();

    if (count($data['trending_packages']) > 0) {
        $last = count($data['trending_packages']) - 1; ?>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <?php
                    echo wpdm_ar_th($t, esc_html__('Package', 'download-manager'));
                    echo wpdm_ar_th($t, esc_html__('Growth', 'download-manager'), 'right', '104');
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_values($data['trending_packages']) as $i => $package): ?>
                    <tr>
                        <td style="<?php echo wpdm_ar_td($t, 'left', $i === $last); ?>padding-right:12px;">
                            <a href="<?php echo esc_url($package['url']); ?>" style="<?php echo $link_style; ?>"><?php echo esc_html($package['title']); ?></a>
                            <div style="padding-top:4px;font-size:12px;color:<?php echo $t['muted']; ?>;<?php echo $t['tnum']; ?>">
                                <?php
                                printf(
                                    /* translators: 1: previous period downloads, 2: current period downloads */
                                    esc_html__('%1$s → %2$s downloads', 'download-manager'),
                                    esc_html(number_format_i18n((int) $package['previous_downloads'])),
                                    esc_html(number_format_i18n((int) $package['current_downloads']))
                                );
                                ?>
                            </div>
                        </td>
                        <td style="<?php echo wpdm_ar_td($t, 'right', $i === $last); ?>">
                            <?php echo wpdm_ar_delta($t, (string) $package['growth_text'], 'positive'); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php } else {
        echo wpdm_ar_empty($t, esc_html__('No packages gained momentum during this period.', 'download-manager'));
    }

    $blocks[] = wpdm_ar_section($t, esc_html__('Trending packages', 'download-manager'), ob_get_clean(), esc_html__('Fastest growing', 'download-manager'));
}

/* ---------------------------------------------------------- User activity */

if (!empty($data['user_activity'])) {
    $activity = $data['user_activity'];

    ob_start();

    echo wpdm_ar_stat_row($t, [
        [esc_html__('New users', 'download-manager'), esc_html(number_format_i18n((int) $activity['new_users'])), ''],
        [esc_html__('Unique downloaders', 'download-manager'), esc_html(number_format_i18n((int) $activity['unique_downloaders'])), ''],
        [
            esc_html__('Registered', 'download-manager'),
            esc_html(number_format_i18n((int) $activity['registered_downloaders'])),
            isset($activity['registered_ratio'])
                ? sprintf(
                    /* translators: %s: percentage of downloaders who were signed in */
                    esc_html__('%s%% of downloaders', 'download-manager'),
                    '<span style="' . $t['tnum'] . '">' . esc_html(number_format_i18n((float) $activity['registered_ratio'], 1)) . '</span>'
                )
                : '',
        ],
        [esc_html__('Guests', 'download-manager'), esc_html(number_format_i18n((int) $activity['guest_downloaders'])), ''],
    ]);

    if (!empty($activity['top_downloaders'])) {
        $last = count($activity['top_downloaders']) - 1; ?>
        <div style="padding-top:20px;">
            <div style="padding-bottom:10px;font-family:<?php echo $t['font']; ?>;font-size:12px;font-weight:700;color:<?php echo $t['ink']; ?>;">
                <?php esc_html_e('Top downloaders', 'download-manager'); ?>
            </div>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        <?php
                        echo wpdm_ar_th($t, esc_html__('User', 'download-manager'));
                        echo wpdm_ar_th($t, esc_html__('Downloads', 'download-manager'), 'right', '96');
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_values($activity['top_downloaders']) as $i => $user): ?>
                        <tr>
                            <td style="<?php echo wpdm_ar_td($t, 'left', $i === $last); ?>padding-right:12px;">
                                <div style="font-weight:600;color:<?php echo $t['ink']; ?>;"><?php echo esc_html($user['name']); ?></div>
                                <div style="padding-top:2px;font-size:12px;color:<?php echo $t['muted']; ?>;"><?php echo esc_html($user['email']); ?></div>
                            </td>
                            <td style="<?php echo wpdm_ar_td($t, 'right', $i === $last); ?>font-weight:700;color:<?php echo $t['ink']; ?>;<?php echo $t['tnum']; ?>">
                                <?php echo esc_html(number_format_i18n((int) $user['downloads'])); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php }

    $blocks[] = wpdm_ar_section($t, esc_html__('User activity', 'download-manager'), ob_get_clean(), esc_html__('Audience', 'download-manager'));
}

/* ----------------------------------------------------- Category breakdown */

if (!empty($data['category_breakdown'])) {
    ob_start();

    if (count($data['category_breakdown']) > 0) {
        $categories = array_slice($data['category_breakdown'], 0, 10);
        $last       = count($categories) - 1; ?>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <?php
                    echo wpdm_ar_th($t, esc_html__('Category', 'download-manager'));
                    echo wpdm_ar_th($t, esc_html__('Downloads', 'download-manager'), 'right', '86');
                    echo wpdm_ar_th($t, esc_html__('Change', 'download-manager'), 'right', '86');
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_values($categories) as $i => $category):
                    $share = (float) $category['percentage']; ?>
                    <tr>
                        <td style="<?php echo wpdm_ar_td($t, 'left', $i === $last); ?>padding-right:12px;">
                            <?php if (!empty($category['url']) && !is_wp_error($category['url'])): ?>
                                <a href="<?php echo esc_url($category['url']); ?>" style="<?php echo $link_style; ?>"><?php echo esc_html($category['name']); ?></a>
                            <?php else: ?>
                                <span style="font-weight:600;color:<?php echo $t['ink']; ?>;"><?php echo esc_html($category['name']); ?></span>
                            <?php endif; ?>
                            <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="padding-top:8px;">
                                <tr>
                                    <td style="vertical-align:middle;"><?php echo wpdm_ar_bar($t, $share); ?></td>
                                    <td width="44" align="right" style="vertical-align:middle;padding-left:8px;font-family:<?php echo $t['font']; ?>;font-size:11px;font-weight:600;color:<?php echo $t['muted']; ?>;<?php echo $t['tnum']; ?>">
                                        <?php echo esc_html(number_format_i18n($share, 1)); ?>%
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="<?php echo wpdm_ar_td($t, 'right', $i === $last); ?>font-weight:700;color:<?php echo $t['ink']; ?>;<?php echo $t['tnum']; ?>">
                            <?php echo esc_html(number_format_i18n((int) $category['downloads'])); ?>
                        </td>
                        <td style="<?php echo wpdm_ar_td($t, 'right', $i === $last); ?>">
                            <?php echo wpdm_ar_delta($t, (string) $category['change']); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php } else {
        echo wpdm_ar_empty($t, esc_html__('No category data is available for this period.', 'download-manager'));
    }

    $blocks[] = wpdm_ar_section($t, esc_html__('Category breakdown', 'download-manager'), ob_get_clean(), esc_html__('Distribution', 'download-manager'));
}

/* ----------------------------------------------------------------- Revenue */

if (!empty($data['revenue_summary'])) {
    $revenue = $data['revenue_summary'];
    $delta   = wpdm_ar_delta($t, (string) $revenue['change'], $revenue['change_class'] ?? null);

    ob_start(); ?>
    <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td style="font-family:<?php echo $t['font']; ?>;font-size:34px;line-height:1.1;font-weight:700;color:<?php echo $t['pos_fg']; ?>;letter-spacing:-0.03em;<?php echo $t['tnum']; ?>">
                <?php echo esc_html($revenue['revenue_formatted']); ?>
            </td>
            <td align="right" style="vertical-align:bottom;"><?php echo $delta; ?></td>
        </tr>
        <tr>
            <td colspan="2" style="padding-top:6px;font-family:<?php echo $t['font']; ?>;font-size:13px;color:<?php echo $t['muted']; ?>;">
                <?php esc_html_e('Total revenue for the period', 'download-manager'); ?>
            </td>
        </tr>
    </table>
    <div style="padding-top:18px;">
        <?php
        echo wpdm_ar_stat_row($t, [
            [esc_html__('Orders', 'download-manager'), esc_html(number_format_i18n((int) $revenue['orders'])), ''],
            [esc_html__('Average order', 'download-manager'), esc_html($revenue['average_order']), ''],
        ]);
        ?>
    </div>

    <?php if (!empty($revenue['top_products'])):
        $last = count($revenue['top_products']) - 1; ?>
        <div style="padding-top:20px;">
            <div style="padding-bottom:10px;font-family:<?php echo $t['font']; ?>;font-size:12px;font-weight:700;color:<?php echo $t['ink']; ?>;">
                <?php esc_html_e('Top selling products', 'download-manager'); ?>
            </div>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        <?php
                        echo wpdm_ar_th($t, esc_html__('Product', 'download-manager'));
                        echo wpdm_ar_th($t, esc_html__('Sold', 'download-manager'), 'right', '64');
                        echo wpdm_ar_th($t, esc_html__('Revenue', 'download-manager'), 'right', '96');
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_values($revenue['top_products']) as $i => $product): ?>
                        <tr>
                            <td style="<?php echo wpdm_ar_td($t, 'left', $i === $last); ?>padding-right:12px;font-weight:600;color:<?php echo $t['ink']; ?>;">
                                <?php echo esc_html($product['title']); ?>
                            </td>
                            <td style="<?php echo wpdm_ar_td($t, 'right', $i === $last); ?><?php echo $t['tnum']; ?>">
                                <?php echo esc_html(number_format_i18n((int) $product['quantity'])); ?>
                            </td>
                            <td style="<?php echo wpdm_ar_td($t, 'right', $i === $last); ?>font-weight:700;color:<?php echo $t['pos_fg']; ?>;<?php echo $t['tnum']; ?>">
                                <?php echo esc_html($revenue['currency'] . number_format_i18n((float) $product['revenue'], 2)); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif;

    $blocks[] = wpdm_ar_section($t, esc_html__('Revenue', 'download-manager'), ob_get_clean(), esc_html__('Sales', 'download-manager'), $t['pos_fg']);
}

/* ----------------------------------------------------------------- Storage */

if (!empty($data['storage_usage'])) {
    $storage = $data['storage_usage'];

    ob_start();

    echo wpdm_ar_stat_row($t, [
        [esc_html__('Total size', 'download-manager'), esc_html($storage['total_size']), ''],
        [esc_html__('Files', 'download-manager'), esc_html(number_format_i18n((int) $storage['file_count'])), ''],
        [esc_html__('Packages', 'download-manager'), esc_html(number_format_i18n((int) $storage['package_count'])), ''],
        [esc_html__('New this period', 'download-manager'), esc_html(number_format_i18n((int) $storage['new_packages'])), ''],
    ]);

    if (!empty($storage['largest_packages'])) {
        $last = count($storage['largest_packages']) - 1; ?>
        <div style="padding-top:20px;">
            <div style="padding-bottom:10px;font-family:<?php echo $t['font']; ?>;font-size:12px;font-weight:700;color:<?php echo $t['ink']; ?>;">
                <?php esc_html_e('Largest packages', 'download-manager'); ?>
            </div>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        <?php
                        echo wpdm_ar_th($t, esc_html__('Package', 'download-manager'));
                        echo wpdm_ar_th($t, esc_html__('Size', 'download-manager'), 'right', '96');
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_values($storage['largest_packages']) as $i => $package): ?>
                        <tr>
                            <td style="<?php echo wpdm_ar_td($t, 'left', $i === $last); ?>padding-right:12px;font-weight:600;color:<?php echo $t['ink']; ?>;">
                                <?php echo esc_html($package['title']); ?>
                            </td>
                            <td style="<?php echo wpdm_ar_td($t, 'right', $i === $last); ?>font-weight:700;color:<?php echo $t['ink']; ?>;<?php echo $t['tnum']; ?>">
                                <?php echo esc_html($package['size']); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php }

    $blocks[] = wpdm_ar_section($t, esc_html__('Storage', 'download-manager'), ob_get_clean(), esc_html__('Library', 'download-manager'));
}
?>
<table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="font-family:<?php echo $t['font']; ?>;">

    <tr>
        <td style="padding-bottom:24px;">
            <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="font-family:<?php echo $t['font']; ?>;font-size:11px;font-weight:600;color:<?php echo $t['accent']; ?>;text-transform:uppercase;letter-spacing:0.1em;">
                        <?php echo esc_html($data['period_label']); ?>
                    </td>
                </tr>
                <tr>
                    <td style="padding-top:6px;font-family:<?php echo $t['font']; ?>;font-size:26px;line-height:1.2;font-weight:700;color:<?php echo $t['ink']; ?>;letter-spacing:-0.025em;">
                        <?php esc_html_e('Activity Report', 'download-manager'); ?>
                    </td>
                </tr>
                <tr>
                    <td style="padding-top:6px;font-family:<?php echo $t['font']; ?>;font-size:13px;color:<?php echo $t['muted']; ?>;">
                        <?php echo esc_html($data['date_range']); ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <?php if (empty($blocks)): ?>
        <tr>
            <td><?php echo wpdm_ar_empty($t, esc_html__('There is no activity to report for this period.', 'download-manager')); ?></td>
        </tr>
    <?php else: ?>
        <?php foreach ($blocks as $i => $block): ?>
            <tr>
                <td style="padding-bottom:<?php echo $i === count($blocks) - 1 ? '0' : '16'; ?>px;"><?php echo $block; ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>

    <tr>
        <td style="padding-top:24px;">
            <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-top:1px solid <?php echo $t['line']; ?>;">
                <tr>
                    <td style="padding-top:14px;font-family:<?php echo $t['font']; ?>;font-size:12px;line-height:1.6;color:<?php echo $t['muted']; ?>;">
                        <?php esc_html_e('Generated automatically by WordPress Download Manager.', 'download-manager'); ?>
                    </td>
                    <td align="right" style="padding-top:14px;white-space:nowrap;">
                        <a href="<?php echo esc_url(admin_url('edit.php?post_type=wpdmpro&page=settings&tab=activity-reports')); ?>" style="font-family:<?php echo $t['font']; ?>;font-size:12px;<?php echo $link_style; ?>">
                            <?php esc_html_e('Report settings', 'download-manager'); ?> &rarr;
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

</table>
