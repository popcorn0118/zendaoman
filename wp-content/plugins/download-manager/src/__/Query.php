<?php
/**
 * Query class for wpdm packages
 */

namespace WPDM\__;


class Query
{
    /**
     * @var string Query only wpdm packages
     */
    private $post_type = "wpdmpro";
    /**
     * @var array Query parameters
     */
    public $params = [];
    /**
     * @var array Query $result
     */
    public $result;
    /**
     * @var array Found packages
     */
    public $packages = [];
    /**
     * @var int Total count
     */
    public $count = 0;

    /**
     * @var string WPDM Tag
     */
    public $tag_tax = WPDM_TAG;

    public $tax_relation = null;


    function __construct()
    {

    }

    /**
     * Static factory method for convenient instantiation
     * @return Query
     */
    static function create()
    {
        return new self();
    }

    /**
     * Reset query for reuse
     * @return $this
     */
    function reset()
    {
        $this->params = [];
        $this->result = null;
        $this->packages = [];
        $this->count = 0;
        $this->tax_relation = null;
        return $this;
    }

    /**
     * @param int $items_per_page
     */
    function items_per_page($items_per_page = 10)
    {
		if($items_per_page)
			$this->params['posts_per_page'] = $items_per_page;
        return $this;
    }

    /**
     * @param string $order_field
     * @param string $order
     */
    function sort($order_field = 'date', $order = 'DESC')
    {

		if($order_field === 'downloads') $order_field = 'download_count';
		if($order_field === 'views') $order_field = 'view_count';
		if(in_array($order_field, ['update_date', 'updated'])) $order_field = 'modified';

        $order_fields = ['__wpdm_download_count', '__wpdm_view_count', '__wpdm_package_size_b'];
        if (!in_array("__wpdm_" . $order_field, $order_fields)) {
            $this->params['orderby'] = $order_field;
            $this->params['order'] = $order;
        } else {
            $this->params['orderby'] = 'meta_value_num';
            $this->params['meta_key'] = "__wpdm_" . $order_field;
            $this->params['order'] = $order;
        }
        return $this;

    }

    /**
     * @param $taxonomy
     * @param $terms
     * @param string $field
     * @param string $operator
     * @param false $include_children
     */
    function taxonomy($taxonomy, $terms, $field = 'slug', $operator = 'IN', $include_children = null)
    {
        if (!isset($this->params['tax_query']) || !is_array($this->params['tax_query'])) $this->params['tax_query'] = [];
        if (!is_array($terms)) $terms = explode(",", $terms);
        $tax_query = [
            'taxonomy' => $taxonomy,
            'field' => $field,
            'terms' => $terms,
        ];
        if ($include_children !== null && $include_children !== '')
            $tax_query['include_children'] = $include_children;
        if ($operator !== 'IN')
            $tax_query['operator'] = $operator;
	    if( isset( $tax_query['operator'] ) && $tax_query['operator'] === '' )
		    $tax_query['operator'] = 'IN';
        if ($taxonomy === 'wpdmcategory') {
            array_unshift($this->params['tax_query'], $tax_query);
        } else
            $this->params['tax_query'][] = $tax_query;
        return $this;
    }

    /**
     * @param string $relation
     */
    function taxonomy_relation($relation = 'OR')
    {
        $this->tax_relation = $relation;
        return $this;
    }

    /**
     * @param null $categories
     * @param string $field
     * @param string $operator
     * @param false $include_children
     */
    function categories($categories = null, $field = 'slug', $operator = 'IN', $include_children = false)
    {
        if ($categories) {
            $this->taxonomy('wpdmcategory', $categories, $field, $operator, $include_children);
        }
        return $this;
    }

    /**
     * Exclude categories
     * @param null $categories
     * @param string $field
     */
    function xcats($categories = null, $field = 'slug')
    {
        if ($categories) {
            if($field === 'slug') {
                if(!is_array($categories)) $categories = explode(",", $categories);
                // Batch lookup instead of N+1 queries
                $terms = get_terms([
                    'taxonomy' => 'wpdmcategory',
                    'slug' => $categories,
                    'fields' => 'ids',
                    'hide_empty' => false,
                ]);
                if (!is_wp_error($terms) && !empty($terms)) {
                    $categories = $terms;
                }
                $field = 'term_id';
            }
            $this->taxonomy('wpdmcategory', $categories, $field, 'NOT IN');
        }
        return $this;
    }

    /**
     * @param null $tags
     * @param string $field
     * @param string $operator
     */
    function tags($tags = null, $field = 'slug', $operator = 'IN')
    {
        if ($tags) {
            $this->taxonomy($this->tag_tax, $tags, $field, $operator);
        }
        return $this;
    }

    /**
     * @param null $tags
     * @param string $field
     */
    function tag__and($tags = null, $field = 'slug')
    {
        if ($tags) {
            $this->taxonomy($this->tag_tax, $tags, $field, 'AND');
        }
        return $this;
    }

    /**
     * @param null $tags
     */
    function tag_slug__and($tags = null)
    {
        if ($tags) {
            $this->taxonomy($this->tag_tax, $tags, 'slug', 'AND');
        }
        return $this;
    }

    /**
     * @param null $tags
     * @param string $field
     */
    function tag__in($tags = null, $field = 'slug')
    {
        if ($tags) {
            $this->taxonomy($this->tag_tax, $tags, $field, 'IN');
        }
        return $this;
    }

    /**
     * @param null $tags
     */
    function tag_slug__in($tags = null)
    {
        if ($tags) {
            $this->taxonomy($this->tag_tax, $tags, 'slug', 'IN');
        }
        return $this;
    }

    /**
     * @param null $tags
     * @param string $field
     */
    function tag__not_in($tags = null, $field = 'slug')
    {
        if ($tags) {
            $this->taxonomy($this->tag_tax, $tags, $field, 'NOT IN');
        }
        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @param string $compare
     */
    function meta($key, $value, $compare = 'LIKE')
    {
        if (!isset($this->params['meta_query']) || !is_array($this->params['meta_query'])) $this->params['meta_query'] = [];
        $this->params['meta_query'][] = [
            'key' => $key,
            'value' => $value,
            'compare' => $compare
        ];
        return $this;
    }

    /**
     * @param string $relation
     */
    function meta_relation($relation = 'OR')
    {
        $this->params['meta_query']['relation'] = $relation;
        return $this;
    }

    /**
     * From date filter
     * @param null $date
     * @param bool $modified Use post_modified_gmt instead of post_date
     * @return $this
     */
    function from_date($date = null, $modified = false)
    {
        if($date)
        {
            $date = explode("-", $date);
            $this->params['date_query']['inclusive'] = true;
            if($modified)
                $this->params['date_query']['column'] = 'post_modified_gmt';
            $this->params['date_query']['after']['year'] = $date[0];
            if(isset($date[1]))
                $this->params['date_query']['after']['month'] = $date[1];
            if(isset($date[2]))
                $this->params['date_query']['after']['day'] = $date[2];
        }
        return $this;
    }

    /**
     * To date filter
     * @param null $date
     * @param bool $modified Use post_modified_gmt instead of post_date
     * @return $this
     */
    function to_date($date = null, $modified = false)
    {
        if($date)
        {
            $date = explode("-", $date);
            $this->params['date_query']['inclusive'] = true;
            if($modified)
                $this->params['date_query']['column'] = 'post_modified_gmt';
            $this->params['date_query']['before']['year'] = $date[0];
            if(isset($date[1]))
                $this->params['date_query']['before']['month'] = $date[1];
            if(isset($date[2]))
                $this->params['date_query']['before']['day'] = $date[2];
        }
        return $this;
    }

    /**
     * @param $field
     * @param $value
     */
    function filter($field, $value)
    {
        $this->params[$field] = $value;
        return $this;
    }

    /**
     * @param $keyword
     * @return $this
     */
    function s($keyword)
    {
        $this->params['s'] = $keyword;
        return $this;
    }

    /**
     * @param $keyword
     * @return $this
     */
    function search($keyword)
    {
        $this->params['s'] = $keyword;
        return $this;
    }

    /**
     * @param $paged
     * @return $this
     */
    function paged($paged)
    {
        if ($paged <= 1) return $this;
        $this->params['paged'] = $paged;
        return $this;
    }

    /**
     * @param $author_id
     * @return $this
     */
    function author($author_id)
    {
        $this->params['author'] = $author_id;
        return $this;
    }

    /**
     * @param $author_id
     * @return $this
     */
    function post_status($status)
    {
        $this->params['post_status'] = $status;
        return $this;
    }

    /**
     * @param $author_name
     * @return $this
     */
    function author_name($author_name)
    {
        $this->params['author_name'] = $author_name;
        return $this;
    }

    /**
     * @param $author__not_in
     * @return $this
     */
    function author__not_in($author__not_in)
    {
        $this->params['author__not_in'] = $author__not_in;
        return $this;
    }

    /**
     * @return $this
     */
    function process()
    {
        if($this->tax_relation && isset($this->params['tax_query']) && is_array($this->params['tax_query']))
            $this->params['tax_query'] = ['relation' => $this->tax_relation] + $this->params['tax_query'];

        $this->params = apply_filters('wpdm_packages_query_params', $this->params);
        $this->params['post_type'] = $this->post_type;
        $this->params['suppress_filters'] = true;
	    $this->result = new \WP_Query($this->params);
		$this->packages = $this->result->posts;
        $this->count = $this->result->found_posts;

        // Prime caches for better performance when accessing package data
        if (!empty($this->packages)) {
            $post_ids = wp_list_pluck($this->packages, 'ID');
            update_postmeta_cache($post_ids);
            update_object_term_cache($post_ids, $this->post_type);
        }

        wp_reset_postdata();
        return $this;
    }

    /**
     * @return array
     */
    function packages()
    {
        return $this->packages;
    }


}
