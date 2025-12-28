# Tailwind CSS Integration Guide

## Overview

This SMM Panel script now includes **Tailwind CSS v3.4** fully integrated and compiled locally (no CDN dependencies). Tailwind works alongside Bootstrap without conflicts using a `tw-` prefix strategy.

## Installation & Setup

### Prerequisites
- Node.js (v14+)
- npm (v6+)

### Initial Setup

1. Install dependencies:
```bash
npm install
```

This will install:
- `tailwindcss` - Utility-first CSS framework
- `postcss` - CSS transformer
- `autoprefixer` - Auto-add vendor prefixes

### Build Commands

#### Production Build (Minified)
```bash
npm run build:css
```
This compiles `assets/css/tailwind-input.css` → `assets/css/tailwind-output.css` (minified)

#### Development Mode (Watch)
```bash
npm run watch:css
```
This watches for changes and auto-rebuilds Tailwind CSS.

#### Quick Dev Alias
```bash
npm run dev
```

## Configuration

### Tailwind Config (`tailwind.config.js`)

Key features:
- **Prefix**: All Tailwind classes use `tw-` prefix (e.g., `tw-flex`, `tw-bg-blue-500`)
- **Content paths**: Scans all PHP files for Tailwind classes
- **Custom colors**: Matches existing brand colors
- **Dark mode**: Class-based dark mode support
- **Custom components**: Pre-built modern components

### PostCSS Config (`postcss.config.js`)

Handles:
- Tailwind processing
- Autoprefixer for browser compatibility

## Usage Guide

### Class Prefix

**Important**: All Tailwind classes must be prefixed with `tw-` to avoid Bootstrap conflicts.

#### ✅ Correct
```html
<div class="tw-flex tw-items-center tw-gap-4">
  <button class="tw-btn-primary">Submit</button>
</div>
```

#### ❌ Incorrect (will conflict with Bootstrap)
```html
<div class="flex items-center gap-4">
  <button class="btn-primary">Submit</button>
</div>
```

### Pre-built Components

#### Modern Card
```html
<div class="tw-card-modern tw-p-6">
  <h3 class="tw-text-xl tw-font-bold tw-mb-4">Card Title</h3>
  <p class="tw-text-gray-600">Card content goes here</p>
</div>
```

#### Modern Button
```html
<button class="tw-btn-primary">Primary Button</button>
<button class="tw-btn-success">Success Button</button>
<button class="tw-btn-danger">Danger Button</button>
```

#### Modern Input
```html
<input type="text" class="tw-input-modern" placeholder="Enter text...">
```

#### Modern Table
```html
<table class="tw-table-modern">
  <thead>
    <tr>
      <th>Column 1</th>
      <th>Column 2</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Data 1</td>
      <td>Data 2</td>
    </tr>
  </tbody>
</table>
```

#### Alerts
```html
<div class="tw-alert-success">Success message</div>
<div class="tw-alert-danger">Error message</div>
<div class="tw-alert-info">Info message</div>
<div class="tw-alert-warning">Warning message</div>
```

### Custom Gradients

```html
<div class="tw-gradient-primary tw-p-6 tw-text-white">
  Gradient background
</div>

<h1 class="tw-text-gradient-primary tw-text-4xl tw-font-bold">
  Gradient text
</h1>
```

### Utility Classes Examples

#### Flexbox Layout
```html
<div class="tw-flex tw-justify-between tw-items-center tw-gap-4">
  <div>Left</div>
  <div>Right</div>
</div>
```

#### Grid Layout
```html
<div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-6">
  <div>Item 1</div>
  <div>Item 2</div>
  <div>Item 3</div>
</div>
```

#### Responsive Design
```html
<div class="tw-text-sm md:tw-text-base lg:tw-text-lg tw-p-4 md:tw-p-6 lg:tw-p-8">
  Responsive sizing
</div>
```

#### Spacing
```html
<div class="tw-m-4 tw-p-6 tw-space-y-4">
  <div>Item with spacing</div>
  <div>Another item</div>
</div>
```

#### Colors
```html
<div class="tw-bg-primary tw-text-white tw-p-4">Primary color</div>
<div class="tw-bg-success tw-text-white tw-p-4">Success color</div>
<div class="tw-bg-danger tw-text-white tw-p-4">Danger color</div>
```

#### Shadows
```html
<div class="tw-shadow-card hover:tw-shadow-card-hover tw-p-6">
  Card with hover effect
</div>
```

#### Animations
```html
<div class="tw-animate-fade-in">Fades in on load</div>
<div class="tw-hover-lift">Lifts up on hover</div>
```

### Dark Mode

Enable dark mode by adding `dark` class to `<html>` or `<body>`:

```html
<html class="dark">
```

Then use dark mode utilities:
```html
<div class="tw-bg-white dark:tw-bg-gray-800 tw-text-gray-900 dark:tw-text-white">
  Content adapts to theme
</div>
```

## Bootstrap Compatibility

### Mixing Bootstrap and Tailwind

You can use both frameworks together:

```html
<!-- Bootstrap components work as before -->
<div class="container">
  <div class="row">
    <div class="col-md-6">
      <!-- Tailwind utilities inside Bootstrap layout -->
      <div class="tw-card-modern tw-p-6">
        <button class="btn btn-primary">Bootstrap Button</button>
        <button class="tw-btn-primary">Tailwind Button</button>
      </div>
    </div>
  </div>
</div>
```

### Best Practices

1. **Layout**: Use Bootstrap's grid system (`container`, `row`, `col-*`)
2. **Components**: Use Bootstrap components (modals, dropdowns, etc.)
3. **Utilities**: Use Tailwind for spacing, colors, typography, flexbox
4. **Custom styling**: Prefer Tailwind utilities over custom CSS

## File Structure

```
smm-panel-script/
├── assets/
│   └── css/
│       ├── tailwind-input.css     # Source file (edit this)
│       └── tailwind-output.css    # Compiled file (auto-generated)
├── tailwind.config.js              # Tailwind configuration
├── postcss.config.js               # PostCSS configuration
├── package.json                    # Dependencies & scripts
└── node_modules/                   # Dependencies (git-ignored)
```

## Customization

### Adding Custom Colors

Edit `tailwind.config.js`:

```javascript
theme: {
  extend: {
    colors: {
      'custom-blue': '#1234ef',
    }
  }
}
```

Usage: `tw-bg-custom-blue`, `tw-text-custom-blue`

### Adding Custom Components

Edit `assets/css/tailwind-input.css`:

```css
@layer components {
  .tw-my-component {
    @apply tw-px-4 tw-py-2 tw-bg-blue-500 tw-text-white tw-rounded;
  }
}
```

After adding, rebuild:
```bash
npm run build:css
```

## Troubleshooting

### Classes Not Working?

1. Ensure class has `tw-` prefix
2. Rebuild CSS: `npm run build:css`
3. Clear browser cache (Ctrl+F5)
4. Check `tailwind.config.js` content paths include your files

### Build Errors?

```bash
# Reinstall dependencies
rm -rf node_modules package-lock.json
npm install

# Rebuild
npm run build:css
```

### Conflicts with Bootstrap?

- Always use `tw-` prefix for Tailwind classes
- Bootstrap classes work without prefix
- If issues persist, check CSS load order in `template.php`

## Performance

### Production Optimization

The build process:
1. Scans all PHP files for used Tailwind classes
2. Generates only the CSS needed (tree-shaking)
3. Minifies output for smallest file size
4. Result: ~5KB minified CSS (vs 3MB+ full Tailwind)

### Best Practices

1. **Only use classes you need** - Tailwind purges unused CSS
2. **Don't use string concatenation** - Write full class names:
   - ✅ Good: `tw-text-primary`
   - ❌ Bad: `tw-text-${'primary'}`
3. **Rebuild after adding new classes** - Run `npm run build:css`

## Version Information

- **Tailwind CSS**: v3.4.17
- **PostCSS**: v8.4.49
- **Autoprefixer**: v10.4.20

## Resources

- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Tailwind Play (Test classes)](https://play.tailwindcss.com)
- [Tailwind UI Components](https://tailwindui.com/components)

## Support

For issues or questions:
1. Check this documentation
2. Review Tailwind official docs
3. Check `tailwind.config.js` configuration
4. Ensure dependencies are installed (`npm install`)

---

**Note**: Tailwind CSS is fully offline. No CDN or internet connection required after `npm install`.
