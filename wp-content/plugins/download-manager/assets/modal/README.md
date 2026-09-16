# WPDM Dialog System

Enterprise-grade modal dialogs for WordPress Download Manager.

## Overview

The WPDM Dialog System provides a modern, accessible, and customizable dialog API for displaying alerts, confirmations, prompts, and custom dialogs in your WordPress Download Manager implementations.

## Features

- **Promise-based API** - All methods return Promises for easy async/await usage
- **Dark Mode Support** - Automatically detects and adapts to light/dark themes
- **Accessible** - Proper ARIA attributes, keyboard navigation, and focus management
- **Responsive** - Mobile-friendly design with bottom sheet on small screens
- **Customizable** - Multiple sizes, icon types, and button configurations
- **XSS Safe** - HTML escaping by default, opt-in for HTML content

## Quick Start

```javascript
// Alert
WPDM.dialog.alert('Welcome!', 'Thanks for using WordPress Download Manager.');

// Confirm
const confirmed = await WPDM.dialog.confirm('Delete File', 'Are you sure you want to delete this file?');
if (confirmed) {
    // User clicked Confirm
}

// Prompt
const filename = await WPDM.dialog.prompt('Rename File', 'Enter a new name for this file:', {
    inputValue: 'document.pdf',
    placeholder: 'Enter filename'
});
if (filename) {
    // User submitted: filename
}

// AJAX - Load content from server
await WPDM.dialog.ajax('Package Details', '/api/package/123', {
    size: 'lg',
    buttons: [{ text: 'Close', type: 'primary', action: 'close' }]
});
```

## API Reference

### `WPDM.dialog.alert(title, message, options)`

Shows an informational alert dialog with a single OK button.

**Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| `title` | string | Dialog title |
| `message` | string | Dialog message |
| `options` | object | Additional options (optional) |

**Options:**
| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `type` | string | `'info'` | Icon type: `info`, `success`, `warning`, `danger` |
| `buttonText` | string | `'OK'` | Button text |
| `size` | string | `'sm'` | Dialog size: `sm`, `md`, `lg`, `xl` |
| `html` | boolean | `false` | Allow HTML in message |

**Returns:** `Promise<boolean>` - Always resolves to `true`

**Example:**
```javascript
await WPDM.dialog.alert('Success', 'Your file has been uploaded.', {
    type: 'success'
});
```

---

### `WPDM.dialog.success(title, message, options)`

Shorthand for alert with success icon.

```javascript
await WPDM.dialog.success('Saved!', 'Your changes have been saved.');
```

---

### `WPDM.dialog.warning(title, message, options)`

Shorthand for alert with warning icon.

```javascript
await WPDM.dialog.warning('Warning', 'This action cannot be undone.');
```

---

### `WPDM.dialog.error(title, message, options)`

Shorthand for alert with danger/error icon.

```javascript
await WPDM.dialog.error('Error', 'Failed to save the file. Please try again.');
```

---

### `WPDM.dialog.confirm(title, message, options)`

Shows a confirmation dialog with Cancel and Confirm buttons.

**Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| `title` | string | Dialog title |
| `message` | string | Dialog message |
| `options` | object | Additional options (optional) |

**Options:**
| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `type` | string | `'question'` | Icon type |
| `confirmText` | string | `'Confirm'` | Confirm button text |
| `cancelText` | string | `'Cancel'` | Cancel button text |
| `confirmType` | string | `'primary'` | Confirm button style: `primary`, `success`, `danger` |
| `size` | string | `'sm'` | Dialog size |
| `html` | boolean | `false` | Allow HTML in message |

**Returns:** `Promise<boolean>` - `true` if confirmed, `false` otherwise

**Example:**
```javascript
const proceed = await WPDM.dialog.confirm(
    'Publish Package',
    'Are you sure you want to publish this package?',
    {
        confirmText: 'Publish',
        cancelText: 'Not Yet'
    }
);

if (proceed) {
    // Publish the package
}
```

---

### `WPDM.dialog.confirmDelete(title, message, options)`

Shorthand for confirm with danger styling (for delete operations).

```javascript
const shouldDelete = await WPDM.dialog.confirmDelete(
    'Delete Package',
    'This will permanently delete the package and all associated files.'
);

if (shouldDelete) {
    // Delete the package
}
```

---

### `WPDM.dialog.prompt(title, message, options)`

Shows a dialog with an input field.

**Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| `title` | string | Dialog title |
| `message` | string | Dialog message (optional) |
| `options` | object | Additional options (optional) |

**Options:**
| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `type` | string | `'question'` | Icon type |
| `inputType` | string | `'text'` | Input type: `text`, `email`, `password`, `number`, `url` |
| `inputValue` | string | `''` | Initial input value |
| `placeholder` | string | `''` | Input placeholder |
| `confirmText` | string | `'Submit'` | Submit button text |
| `cancelText` | string | `'Cancel'` | Cancel button text |
| `size` | string | `'md'` | Dialog size |
| `html` | boolean | `false` | Allow HTML in message |

**Returns:** `Promise<string|null>` - Input value if submitted, `null` if cancelled

**Example:**
```javascript
const newName = await WPDM.dialog.prompt(
    'Rename Package',
    'Enter a new name for this package:',
    {
        inputValue: 'My Package',
        placeholder: 'Package name'
    }
);

if (newName !== null) {
    // Rename to: newName
}
```

---

### `WPDM.dialog.ajax(title, urlOrOptions, options)`

Shows a dialog with content loaded asynchronously via AJAX. Displays a loading spinner while fetching content.

**Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| `title` | string | Dialog title |
| `urlOrOptions` | string\|object | URL string or AJAX options object |
| `options` | object | Additional dialog options (optional) |

**AJAX Options (when `urlOrOptions` is an object):**
| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `url` | string | Required | URL to fetch content from |
| `method` | string | `'GET'` | HTTP method |
| `data` | object | `{}` | Data to send with request |
| `dataType` | string | `'html'` | Expected response type: `html`, `json` |
| `headers` | object | `{}` | Additional headers |
| `timeout` | number | `30000` | Request timeout in ms |

**Dialog Options:**
| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `type` | string | `'info'` | Icon type |
| `size` | string | `'md'` | Dialog size |
| `subtitle` | string | - | Subtitle below title |
| `closable` | boolean | `true` | Show close button |
| `backdrop` | string | - | `'static'` to prevent backdrop close |
| `keyboard` | boolean | `true` | Allow Escape key to close |
| `buttons` | array | - | Footer buttons (shown after content loads) |
| `errorMessage` | string | `'Failed to load...'` | Custom error message |
| `showRetry` | boolean | `true` | Show retry button on error |
| `onLoad` | function | - | Callback after content loads: `($body, data)` |
| `onError` | function | - | Callback on error: `(xhr, status, error)` |

**JSON Response Format:**
When `dataType: 'json'`, the response can include:
```javascript
{
    content: '<html>',    // or 'html' or 'body'
    buttons: [            // Optional: override dialog buttons
        { text: 'OK', type: 'primary', action: 'ok' }
    ]
}
```

**Returns:** `Promise<{action: string, data: any}>` - Result object with action and response data

**Examples:**

```javascript
// Simple URL
await WPDM.dialog.ajax('Package Details', '/api/package/123');

// With options
await WPDM.dialog.ajax('User Profile', '/api/user/details', {
    size: 'lg',
    type: 'info',
    buttons: [
        { text: 'Close', type: 'secondary', action: 'close' },
        { text: 'Edit', type: 'primary', action: 'edit' }
    ]
});

// POST request with data
const result = await WPDM.dialog.ajax('Search Results', {
    url: ajaxurl,
    method: 'POST',
    data: { action: 'wpdm_search', query: 'documents' }
}, {
    size: 'xl',
    onLoad: function($body, data) {
        // Initialize any JavaScript in loaded content
        $body.find('.datepicker').datepicker();
    }
});

// Handle button actions
if (result.action === 'edit') {
    // User clicked Edit button
}

// WordPress admin-ajax.php example
await WPDM.dialog.ajax('Package Settings', {
    url: ajaxurl,
    method: 'POST',
    data: {
        action: 'wpdm_get_package_settings',
        package_id: 123,
        _wpnonce: wpdm_nonce
    },
    dataType: 'json'
}, {
    size: 'lg',
    buttons: [
        { text: 'Cancel', type: 'secondary', action: 'cancel' },
        { text: 'Save', type: 'primary', action: 'save' }
    ]
});
```

---

### `WPDM.dialog.load(title, url, options)`

Alias for `ajax()` with simplified parameters.

```javascript
await WPDM.dialog.load('Help', '/help/getting-started');
```

---

### `WPDM.dialog.show(options)`

Advanced method for creating custom dialogs.

**Options:**
| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `title` | string | Required | Dialog title |
| `subtitle` | string | - | Subtitle below title |
| `message` | string | - | Main message content |
| `type` | string | `'info'` | Icon type: `info`, `success`, `warning`, `danger`, `question` |
| `icon` | boolean | `true` | Show/hide icon |
| `size` | string | `'md'` | Size: `sm`, `md`, `lg`, `xl` |
| `input` | boolean | `false` | Show input field |
| `inputType` | string | `'text'` | Input type |
| `inputValue` | string | `''` | Initial input value |
| `placeholder` | string | `''` | Input placeholder |
| `content` | string | - | Custom HTML content |
| `html` | boolean | `false` | Allow HTML in message |
| `closable` | boolean | `true` | Show close button |
| `backdrop` | string | - | `'static'` to prevent backdrop close |
| `keyboard` | boolean | `true` | Allow Escape key to close |
| `buttons` | array | - | Custom button configuration |
| `compactFooter` | boolean | `false` | No background on footer |

**Button Configuration:**
```javascript
buttons: [
    { text: 'Cancel', type: 'secondary', action: 'cancel' },
    { text: 'Save', type: 'primary', action: 'save' },
    { text: 'Delete', type: 'danger', action: 'delete' }
]
```

**Button Types:** `secondary`, `primary`, `success`, `danger`

**Returns:** `Promise<{action: string, value: string|null}>` - Result object with action and input value

**Example:**
```javascript
const result = await WPDM.dialog.show({
    title: 'Configure Download',
    subtitle: 'Set download options',
    type: 'info',
    size: 'lg',
    content: `
        <div class="form-group">
            <label>Max Downloads</label>
            <input type="number" class="form-control" id="max-downloads" value="10">
        </div>
    `,
    buttons: [
        { text: 'Cancel', type: 'secondary', action: 'cancel' },
        { text: 'Apply', type: 'primary', action: 'apply' }
    ]
});

if (result.action === 'apply') {
    const maxDownloads = document.getElementById('max-downloads').value;
    // Apply configuration
}
```

## Dialog Sizes

| Size | Max Width | Best For |
|------|-----------|----------|
| `sm` | 320px | Simple alerts and confirms |
| `md` | 420px | Prompts and small forms |
| `lg` | 560px | Larger forms |
| `xl` | 720px | Complex content |

## Icon Types

| Type | Color | Use Case |
|------|-------|----------|
| `info` | Blue | General information |
| `success` | Green | Success messages |
| `warning` | Orange | Warnings |
| `danger` | Red | Errors, delete confirmations |
| `question` | Purple | Questions, confirmations |

## Dark Mode

The dialog automatically syncs with WPDM's color scheme settings. Detection priority:

1. **WPDM Settings** - `wpdm_js.color_scheme` from WordPress Download Manager settings
   - `'light'` → Forces light mode
   - `'dark'` → Forces dark mode
   - `'system'` → Falls through to check page classes or OS preference
2. **Page Classes** - `.dark-mode` or `.light-mode` class on body or `.w3eden`
3. **Data Attributes** - `data-theme="dark"` or `data-theme="light"` on html/body
4. **System Preference** - CSS `prefers-color-scheme` media query

### WPDM Color Scheme Setting

The color scheme is configured in **WPDM → Settings → User Interface** and stored as `__wpdm_color_scheme` option with values:
- `light` - Always use light mode
- `dark` - Always use dark mode
- `system` - Follow operating system preference

## Keyboard Shortcuts

| Key | Action |
|-----|--------|
| `Escape` | Close dialog (if keyboard enabled) |
| `Enter` | Submit prompt input |
| `Tab` | Navigate between buttons |

## Accessibility

- Uses `role="dialog"` and `aria-modal="true"`
- Title linked with `aria-labelledby`
- Focus trapped within dialog
- Focus restored on close
- Close button has `aria-label`

## CSS Classes

### Wrapper Classes
- `.wpdm-dialog-wrapper` - Main wrapper
- `.wpdm-dialog-visible` - Added when visible
- `.dark-mode` / `.light-mode` - Theme classes

### Size Classes
- `.wpdm-dialog--sm`
- `.wpdm-dialog--md`
- `.wpdm-dialog--lg`
- `.wpdm-dialog--xl`

### Icon Variant Classes
- `.wpdm-dialog__icon--info`
- `.wpdm-dialog__icon--success`
- `.wpdm-dialog__icon--warning`
- `.wpdm-dialog__icon--danger`
- `.wpdm-dialog__icon--question`

### Button Classes
- `.wpdm-dialog__btn--secondary`
- `.wpdm-dialog__btn--primary`
- `.wpdm-dialog__btn--success`
- `.wpdm-dialog__btn--danger`
- `.wpdm-dialog__btn--loading`

### AJAX State Classes
- `.wpdm-dialog__body--ajax` - Body with loading spinner
- `.wpdm-dialog__loading` - Loading container
- `.wpdm-dialog__spinner` - Animated spinner
- `.wpdm-dialog__loading-text` - Loading text
- `.wpdm-dialog__error` - Error state container
- `.wpdm-dialog__error-icon` - Error icon
- `.wpdm-dialog__error-message` - Error message
- `.wpdm-dialog__retry` - Retry button
- `.wpdm-dialog__footer--hidden` - Hidden footer (until content loads)

## Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

Requires Promise support (IE11 needs polyfill).

## Migration from Native Dialogs

**Before (native):**
```javascript
if (confirm('Delete this file?')) {
    deleteFile();
}
```

**After (WPDM Dialog):**
```javascript
const confirmed = await WPDM.dialog.confirm('Delete', 'Delete this file?');
if (confirmed) {
    deleteFile();
}
```

**Before (native prompt):**
```javascript
const name = prompt('Enter name:', 'Default');
if (name) {
    saveName(name);
}
```

**After (WPDM Dialog):**
```javascript
const name = await WPDM.dialog.prompt('Name', 'Enter name:', { inputValue: 'Default' });
if (name !== null) {
    saveName(name);
}
```

## Standalone Usage

The dialog can be used without the WPDM global object:

```javascript
// WPDMDialog is available as a standalone object
WPDMDialog.alert('Hello', 'World');
```

## Files

| File | Purpose |
|------|---------|
| `assets/modal/wpdm-modal.js` | JavaScript implementation |
| `assets/modal/wpdm-modal.css` | CSS styles |
| `assets/modal/README.md` | This documentation |

## Installation / Enqueue

### In PHP (WordPress)

```php
// Enqueue the modal library
wp_enqueue_style('wpdm-modal', WPDM_ASSET_URL . 'modal/wpdm-modal.css', [], WPDM_VERSION);
wp_enqueue_script('wpdm-modal', WPDM_ASSET_URL . 'modal/wpdm-modal.js', ['jquery'], WPDM_VERSION, true);
```

### In HTML

```html
<link rel="stylesheet" href="path/to/wpdm-modal.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="path/to/wpdm-modal.js"></script>
```

## Dependencies

- jQuery 1.9+ (required)
