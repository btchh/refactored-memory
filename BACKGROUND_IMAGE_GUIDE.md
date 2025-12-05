# Background Image Setup Guide

## How to Add Your Background Image

### Step 1: Prepare Your Image
- Choose a high-quality image (recommended: 1920x1080 or larger)
- Supported formats: `.jpg`, `.jpeg`, `.png`, `.webp`
- Name your image: `auth-background.jpg` (or any name you prefer)

### Step 2: Place the Image
Put your image in the `public/images/` directory:
```
public/
  └── images/
      └── auth-background.jpg
```

### Step 3: Update the Path (if using a different name)
If you use a different filename, update these files:

**resources/views/user/auth/login.blade.php** (around line 5):
```html
<div class="absolute inset-0 bg-cover bg-center bg-no-repeat" 
     style="background-image: url('{{ asset('images/YOUR-IMAGE-NAME.jpg') }}');">
```

**resources/views/user/auth/register.blade.php** (around line 5):
```html
<div class="absolute inset-0 bg-cover bg-center bg-no-repeat" 
     style="background-image: url('{{ asset('images/YOUR-IMAGE-NAME.jpg') }}');">
```

### Step 4: Adjust the Overlay (Optional)
You can adjust the darkness of the overlay by changing the opacity values:

```html
<!-- Lighter overlay (current setting - less blur, clearer image) -->
<div class="absolute inset-0 bg-gradient-to-br from-blue-900/15 via-purple-900/15 to-pink-900/15"></div>

<!-- Medium overlay -->
<div class="absolute inset-0 bg-gradient-to-br from-blue-900/30 via-purple-900/30 to-pink-900/30"></div>

<!-- Darker overlay -->
<div class="absolute inset-0 bg-gradient-to-br from-blue-900/50 via-purple-900/50 to-pink-900/50"></div>
```

### Current Setup
- **Background Image Path**: `public/images/image.png`
- **Background Size**: `cover` (fills entire screen while maintaining aspect ratio)
- **Background Position**: `center center` (centers the image)
- **Blur Effect**: `3px` subtle blur for a soft, elegant look
- **Scale**: `1.1` (slightly scaled up to prevent blur edge artifacts)
- **Fallback**: Gradient background (blue to purple) if image is not found
- **Overlay**: Light gradient with 15% opacity for clearer image visibility
- **Card Blur**: Removed for sharper appearance
- **Decorative Elements**: Animated floating orbs (can be removed if desired)

### Adjusting the Blur
You can change the blur intensity by modifying the `filter: blur()` value:
- `blur(2px)` - Very subtle blur
- `blur(3px)` - Light blur (current setting)
- `blur(5px)` - Medium blur
- `blur(8px)` - Strong blur
- `blur(0px)` - No blur (remove the filter property)

### How It Works
- The image fills the entire screen without stretching or compressing
- Maintains original aspect ratio (no distortion)
- Subtle blur creates depth and makes text more readable
- Scale(1.1) prevents white edges that can appear with blur filter

### Tips
- Use images with good contrast for better readability
- Avoid busy images with too many details
- Test on both desktop and mobile devices
- Consider using WebP format for better performance
- The overlay helps ensure text remains readable regardless of the background image

### Remove Background Image
To use only the gradient background without an image:
1. Delete or comment out the background image div
2. The fallback gradient will automatically show

### Example Images to Try
- Abstract gradients
- Blurred cityscapes
- Soft geometric patterns
- Nature scenes (mountains, ocean, sky)
- Minimalist textures
