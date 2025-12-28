# Tailwind CSS Integration - Getting Started

## Quick Start

```bash
# 1. Install dependencies (first time only)
npm install

# 2. Build CSS for production
npm run build:css

# 3. That's it! Your site now has Tailwind CSS
```

## What's New?

✅ **Tailwind CSS v3.4.17** integrated (fully offline)  
✅ **Bootstrap compatibility** maintained (no conflicts)  
✅ **Modern UI components** ready to use  
✅ **Comprehensive documentation** included  

## Documentation

- 📖 **[TAILWIND_CSS_GUIDE.md](TAILWIND_CSS_GUIDE.md)** - Complete usage guide
- 📋 **[TAILWIND_QUICK_REFERENCE.md](TAILWIND_QUICK_REFERENCE.md)** - Quick reference cheat sheet
- 📊 **[TAILWIND_IMPLEMENTATION_SUMMARY.md](TAILWIND_IMPLEMENTATION_SUMMARY.md)** - Implementation details
- 🎨 **[tailwind-showcase.html](tailwind-showcase.html)** - Live component examples

## Usage Example

### Mix Bootstrap and Tailwind

```html
<!-- Bootstrap grid system -->
<div class="container">
  <div class="row">
    <div class="col-md-6">
      <!-- Tailwind utilities with tw- prefix -->
      <div class="tw-card-modern tw-p-6 hover:tw-shadow-lg">
        <h3 class="tw-text-2xl tw-font-bold tw-mb-4">Modern Card</h3>
        <p class="tw-text-gray-600">Content here</p>
        <button class="tw-btn-primary">Click Me</button>
      </div>
    </div>
  </div>
</div>
```

### Key Rules

1. **Always use `tw-` prefix** for Tailwind classes
2. **Keep Bootstrap classes** without prefix (e.g., `container`, `row`, `col-md-6`)
3. **Can mix freely** - both frameworks work together
4. **Rebuild after changes** to `tailwind-input.css`

## Pre-built Components

### Buttons
```html
<button class="tw-btn-primary">Primary</button>
<button class="tw-btn-success">Success</button>
<button class="tw-btn-danger">Danger</button>
```

### Cards
```html
<div class="tw-card-modern tw-p-6">
  <h3 class="tw-text-xl tw-font-bold">Title</h3>
  <p class="tw-text-gray-600">Content</p>
</div>
```

### Alerts
```html
<div class="tw-alert-success">Success message!</div>
<div class="tw-alert-danger">Error message!</div>
<div class="tw-alert-info">Info message!</div>
```

### Forms
```html
<input type="text" class="tw-input-modern" placeholder="Email">
```

## Development Workflow

### Development Mode (Auto-rebuild)
```bash
npm run dev
```
This watches for changes and rebuilds automatically.

### Production Build
```bash
npm run build:css
```
This creates a minified production build (~5KB).

## Features

- ✅ **Fully Offline** - No CDN dependencies
- ✅ **Bootstrap Compatible** - No conflicts (using `tw-` prefix)
- ✅ **Modern Design** - Contemporary UI components
- ✅ **Performance** - Only 5KB minified CSS
- ✅ **Responsive** - Mobile-first design
- ✅ **Dark Mode** - Class-based dark mode support
- ✅ **Custom Colors** - Brand colors integrated
- ✅ **Documentation** - Comprehensive guides included

## File Structure

```
├── assets/css/
│   ├── tailwind-input.css              # Source (edit this)
│   ├── tailwind-output.css             # Compiled (auto-generated)
│   └── tailwind-bootstrap-enhanced.css # Hybrid enhancements
├── tailwind.config.js                  # Tailwind config
├── postcss.config.js                   # PostCSS config
├── package.json                        # Dependencies
├── TAILWIND_CSS_GUIDE.md              # Complete guide
├── TAILWIND_QUICK_REFERENCE.md        # Quick reference
└── tailwind-showcase.html             # Examples
```

## Common Tasks

### Adding Custom Components

Edit `assets/css/tailwind-input.css`:

```css
@layer components {
  .tw-my-component {
    @apply tw-px-4 tw-py-2 tw-bg-primary tw-text-white tw-rounded-lg;
  }
}
```

Then rebuild:
```bash
npm run build:css
```

### Customizing Colors

Edit `tailwind.config.js`:

```javascript
theme: {
  extend: {
    colors: {
      'custom': '#1234ef',
    }
  }
}
```

Use: `tw-bg-custom`, `tw-text-custom`

## Troubleshooting

**Classes not working?**
```bash
npm run build:css
```

**Conflicts with Bootstrap?**
- Ensure using `tw-` prefix for Tailwind classes

**Build errors?**
```bash
rm -rf node_modules
npm install
npm run build:css
```

## Resources

- 📖 Full Guide: [TAILWIND_CSS_GUIDE.md](TAILWIND_CSS_GUIDE.md)
- 🚀 Quick Ref: [TAILWIND_QUICK_REFERENCE.md](TAILWIND_QUICK_REFERENCE.md)
- 🌐 Examples: [tailwind-showcase.html](tailwind-showcase.html)
- 📚 Official Docs: https://tailwindcss.com/docs

## Screenshot

![Tailwind Showcase](https://github.com/user-attachments/assets/1bfbe620-560d-483e-80d8-3c88f3a774a3)

## Support

For questions or issues:
1. Check the documentation files
2. Review the showcase examples
3. Visit Tailwind CSS official docs

---

**Made with ❤️ using Tailwind CSS + Bootstrap**
