# Tailwind CSS Quick Reference

## 🚀 Getting Started

```bash
# First time setup
npm install

# Build for production
npm run build:css

# Development mode (auto-rebuild)
npm run dev
```

## 📝 Important Rules

1. **Always use `tw-` prefix** for Tailwind classes
2. **Keep Bootstrap classes** as they are (no prefix)
3. **Can mix both** frameworks freely
4. **Rebuild after changes** to tailwind-input.css

## 🎨 Common Patterns

### Flex Layout
```html
<div class="tw-flex tw-items-center tw-justify-between tw-gap-4">
  <div>Left</div>
  <div>Right</div>
</div>
```

### Grid Layout
```html
<div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-6">
  <div>Item 1</div>
  <div>Item 2</div>
  <div>Item 3</div>
</div>
```

### Spacing
```html
tw-p-4          <!-- padding: 1rem -->
tw-m-4          <!-- margin: 1rem -->
tw-px-6         <!-- padding left/right: 1.5rem -->
tw-py-3         <!-- padding top/bottom: 0.75rem -->
tw-space-y-4    <!-- vertical spacing between children -->
tw-gap-4        <!-- gap in flex/grid -->
```

### Colors
```html
tw-bg-primary       <!-- background -->
tw-text-white       <!-- text color -->
tw-border-gray-300  <!-- border color -->
```

### Typography
```html
tw-text-xl          <!-- font size 1.25rem -->
tw-font-bold        <!-- font weight 700 -->
tw-text-center      <!-- text align center -->
tw-uppercase        <!-- text transform -->
```

### Borders & Radius
```html
tw-border           <!-- 1px border -->
tw-border-2         <!-- 2px border -->
tw-rounded-lg       <!-- border radius 0.5rem -->
tw-rounded-full     <!-- fully rounded -->
```

### Shadows
```html
tw-shadow-card              <!-- custom card shadow -->
hover:tw-shadow-card-hover  <!-- hover shadow -->
tw-shadow-md                <!-- medium shadow -->
```

## 🎯 Pre-built Components

### Modern Card
```html
<div class="tw-card-modern tw-p-6">
  <h3 class="tw-text-xl tw-font-bold tw-mb-4">Title</h3>
  <p class="tw-text-gray-600">Content</p>
</div>
```

### Buttons
```html
<button class="tw-btn-primary">Primary</button>
<button class="tw-btn-success">Success</button>
<button class="tw-btn-danger">Danger</button>
```

### Input
```html
<input type="text" class="tw-input-modern" placeholder="Enter text">
```

### Alert
```html
<div class="tw-alert-success">Success message!</div>
<div class="tw-alert-danger">Error message!</div>
<div class="tw-alert-info">Info message!</div>
<div class="tw-alert-warning">Warning message!</div>
```

### Table
```html
<table class="tw-table-modern">
  <thead>
    <tr>
      <th>Header 1</th>
      <th>Header 2</th>
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

### Badge
```html
<span class="tw-badge-modern tw-bg-success tw-text-white">New</span>
```

## 🎨 Gradients

```html
<div class="tw-gradient-primary">Primary gradient</div>
<div class="tw-gradient-success">Success gradient</div>
<div class="tw-gradient-danger">Danger gradient</div>
<div class="tw-gradient-warning">Warning gradient</div>
<div class="tw-gradient-info">Info gradient</div>
```

## 📱 Responsive Design

### Breakpoints
- `sm:` - 640px and up
- `md:` - 768px and up
- `lg:` - 1024px and up
- `xl:` - 1280px and up

### Examples
```html
<!-- Stack on mobile, side-by-side on desktop -->
<div class="tw-flex tw-flex-col md:tw-flex-row">
  <div>Left</div>
  <div>Right</div>
</div>

<!-- 1 column on mobile, 3 on desktop -->
<div class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-3 tw-gap-4">
  <div>Item</div>
  <div>Item</div>
  <div>Item</div>
</div>

<!-- Hide on mobile, show on desktop -->
<div class="tw-hidden md:tw-block">
  Desktop only content
</div>

<!-- Responsive text size -->
<h1 class="tw-text-2xl md:tw-text-4xl lg:tw-text-5xl">
  Responsive heading
</h1>
```

## 🎭 Hover & States

```html
hover:tw-bg-primary         <!-- hover background -->
hover:tw-text-white         <!-- hover text -->
hover:tw-shadow-lg          <!-- hover shadow -->
hover:tw-scale-105          <!-- hover scale up -->
focus:tw-ring-2             <!-- focus ring -->
focus:tw-ring-primary       <!-- focus ring color -->
active:tw-scale-95          <!-- active scale down -->
```

## 🔄 Animations

```html
tw-animate-fade-in          <!-- fade in animation -->
tw-animate-slide-in         <!-- slide in animation -->
tw-hover-lift               <!-- lift on hover -->
tw-transition-all           <!-- smooth transitions -->
tw-duration-300             <!-- 300ms duration -->
```

## 🌓 Dark Mode

```html
<!-- Add 'dark' class to <html> or <body> to enable -->
<div class="tw-bg-white dark:tw-bg-gray-800">
  <p class="tw-text-gray-900 dark:tw-text-white">
    Adapts to theme
  </p>
</div>
```

## 🔧 Mixing Bootstrap + Tailwind

```html
<!-- Bootstrap grid + Tailwind utilities -->
<div class="container">
  <div class="row">
    <div class="col-md-6">
      <div class="tw-card-modern tw-p-6">
        <!-- Bootstrap button -->
        <button class="btn btn-primary">Bootstrap</button>
        
        <!-- Tailwind button -->
        <button class="tw-btn-primary">Tailwind</button>
      </div>
    </div>
  </div>
</div>
```

## 🎨 Custom Colors

```
tw-bg-primary       #467fcf (blue)
tw-bg-success       #5eba00 (green)
tw-bg-danger        #cd201f (red)
tw-bg-warning       #f1c40f (yellow)
tw-bg-info          #45aaf2 (light blue)
tw-bg-azure         #45aaf2
tw-bg-teal          #2bcbba
tw-bg-orange        #fd9644
tw-bg-purple        #a55eea
tw-bg-pink          #f66d9b
```

## ⚡ Performance Tips

1. **Only use classes in HTML** - Tailwind scans for them
2. **Don't use string concatenation** - Write full class names
3. **Rebuild after major changes** - `npm run build:css`
4. **Minified in production** - Only ~5KB total

## 📚 Resources

- Full Guide: `TAILWIND_CSS_GUIDE.md`
- Examples: `tailwind-showcase.html`
- Official Docs: https://tailwindcss.com/docs

## 🐛 Troubleshooting

**Classes not working?**
```bash
npm run build:css
```

**Conflicts with Bootstrap?**
- Ensure using `tw-` prefix

**Build errors?**
```bash
rm -rf node_modules
npm install
npm run build:css
```

## 📝 Cheat Sheet

| Need | Class |
|------|-------|
| Hide element | `tw-hidden` |
| Show element | `tw-block` or `tw-flex` |
| Center text | `tw-text-center` |
| Bold text | `tw-font-bold` |
| White background | `tw-bg-white` |
| Full width | `tw-w-full` |
| Rounded corners | `tw-rounded-lg` |
| Shadow | `tw-shadow-card` |
| Padding all sides | `tw-p-4` |
| Margin top | `tw-mt-4` |
| Flex container | `tw-flex` |
| Grid container | `tw-grid` |

---

**Remember**: Always prefix with `tw-` and rebuild when needed!
