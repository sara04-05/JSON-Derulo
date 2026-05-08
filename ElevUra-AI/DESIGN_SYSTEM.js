/**
 * ElevUra Dashboard - Design System Reference
 * Color Palette, Typography, Spacing, and Animation Guidelines
 */

/* ==================== COLOR SYSTEM ==================== */

const COLORS = {
  // Base Dark Backgrounds
  backgrounds: {
    base: '#050607',        // Darkest - page background
    dark: '#0A0B0D',        // Very dark - sidebar, header
    card: '#0F1115',        // Dark - card background
    cardLight: '#111317',   // Slightly lighter - secondary cards
  },

  // Cyan Accent System (Primary)
  cyan: {
    primary: '#00E5FF',     // Bright neon cyan
    mid: '#00D8FF',         // Mid-tone cyan
    deep: '#00C2FF',        // Deeper cyan
  },

  // Purple Secondary Accents
  purple: {
    accent: '#A78BFA',      // Light purple
    deep: '#8B5CF6',        // Deep purple
  },

  // Text Colors
  text: {
    primary: '#F5F7FA',     // Bright white - headings, primary text
    secondary: '#9CA3AF',   // Muted gray - descriptions
    tertiary: '#6B7280',    // Darker gray - labels, secondary info
  },

  // Borders & Overlays
  borders: {
    subtle: 'rgba(255, 255, 255, 0.06)',   // Almost invisible
    cyan: 'rgba(0, 229, 255, 0.15)',       // Subtle cyan tint
  }
};

/* ==================== TYPOGRAPHY SYSTEM ==================== */

const TYPOGRAPHY = {
  fontFamily: '"Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif',
  
  scales: {
    // Hero Title
    heroTitle: {
      fontSize: '72px',
      fontWeight: 800,
      lineHeight: 1.0,
      letterSpacing: '-1px',
      textTransform: 'none',
    },
    
    // Large Heading
    h1: {
      fontSize: '48px',
      fontWeight: 700,
      lineHeight: 1.1,
      letterSpacing: '-0.5px',
    },
    
    // Section Heading
    h2: {
      fontSize: '32px',
      fontWeight: 700,
      lineHeight: 1.2,
      letterSpacing: '-0.3px',
    },
    
    // Card Title
    h3: {
      fontSize: '24px',
      fontWeight: 700,
      lineHeight: 1.3,
      letterSpacing: '0px',
    },
    
    // Body Large
    body: {
      fontSize: '16px',
      fontWeight: 400,
      lineHeight:+ 1.6,
      letterSpacing: '0px',
    },
    
    // Body Small
    bodySmall: {
      fontSize: '14px',
      fontWeight: 500,
      lineHeight: 1.5,
      letterSpacing: '0px',
    },
    
    // Label/Tag
    label: {
      fontSize: '12px',
      fontWeight: 600,
      lineHeight: 1.4,
      letterSpacing: '0.5px',
      textTransform: 'uppercase',
    },
    
    // Monospace (for metrics)
    monospace: {
      fontFamily: '"Courier New", monospace',
      fontSize: '20px',
      fontWeight: 700,
      lineHeight: 1.2,
    },
  }
};

/* ==================== SPACING SYSTEM ==================== */

const SPACING = {
  // Sidebar
  sidebarWidth: '220px',
  sidebarPadding: '32px 16px',
  
  // Header
  topHeaderHeight: '72px',
  
  // Content
  contentPadding: '48px 32px',
  contentMaxWidth: '1500px',
  
  // Cards
  cardPadding: '32px',
  cardBorderRadius: '20px',
  
  // Hero Panel
  heroPadding: '48px',
  heroBorderRadius: '28px',
  
  // Grid Gaps
  moduleGridGap: '32px',
  analyticsGridGap: '32px',
  
  // Component Spacing
  spacing: {
    xs: '4px',
    sm: '8px',
    md: '12px',
    lg: '16px',
    xl: '24px',
    xxl: '32px',
    xxxl: '48px',
  }
};

/* ==================== SHADOWS & GLOW ==================== */

const EFFECTS = {
  // Subtle glows
  glows: {
    // Small glow - for icons, badges
    small: '0 0 12px rgba(0, 229, 255, 0.2)',
    
    // Medium glow - for cards on hover
    medium: '0 0 20px rgba(0, 229, 255, 0.15)',
    
    // Large glow - for hero section
    large: '0 0 40px rgba(0, 229, 255, 0.08)',
    
    // Very subtle ambient glow
    ambient: '0 0 60px rgba(0, 229, 255, 0.05)',
  },
  
  // Elevation shadows
  shadows: {
    // Inset shadow for input fields
    inset: 'inset 0 2px 8px rgba(0, 0, 0, 0.4)',
    
    // Card shadow
    card: '0 8px 24px rgba(0, 229, 255, 0.08)',
    
    // Hover lift
    hover: '0 12px 32px rgba(0, 229, 255, 0.15)',
  },
  
  // Backdrop effects
  blur: {
    // Header blur
    header: 'backdrop-filter: blur(10px)',
    
    // Deep blur for glass morphism
    deep: 'backdrop-filter: blur(20px)',
  }
};

/* ==================== ANIMATION SYSTEM ==================== */

const ANIMATIONS = {
  // Primary easing for all UI transitions
  easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
  
  // Timing values
  timings: {
    fast: '100ms',
    quick: '150ms',
    standard: '200ms',
    slow: '300ms',
    slower: '400ms',
    slowest: '600ms',
  },
  
  // Keyframe animations
  keyframes: {
    // Floating animation for hero cube
    float: `
      @keyframes floatAnimation {
        0%, 100% { transform: translateY(0px) rotateZ(0deg); }
        50% { transform: translateY(-20px) rotateZ(5deg); }
      }
      animation: floatAnimation 6s ease-in-out infinite;
    `,
    
    // Rotating cube
    rotate: `
      @keyframes rotateInner {
        0% { transform: rotateX(0deg) rotateY(0deg); }
        100% { transform: rotateX(360deg) rotateY(360deg); }
      }
      animation: rotateInner 8s linear infinite;
    `,
    
    // Pulsing glow
    pulse: `
      @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
      }
      animation: pulse 2s infinite;
    `,
    
    // Glow expansion (for status indicator)
    glowPulse: `
      @keyframes glowPulse {
        0%, 100% { opacity: 0.4; }
        50% { opacity: 0.8; }
      }
      animation: glowPulse 3s ease-in-out infinite;
    `,
  },
  
  // Transition presets
  transitions: {
    // For hover states - quick and smooth
    hover: `transition: all 0.3s cubic-bezier(0.22, 1, 0.36, 1);`,
    
    // For fade effects
    fade: `transition: opacity 0.2s ease, color 0.2s ease;`,
    
    // For movement
    move: `transition: transform 0.3s cubic-bezier(0.22, 1, 0.36, 1);`,
    
    // For glow effects
    glow: `transition: box-shadow 0.3s ease, border-color 0.3s ease;`,
  }
};

/* ==================== RESPONSIVE BREAKPOINTS ==================== */

const BREAKPOINTS = {
  // Desktop (default)
  desktop: '1400px',
  
  // Laptop (max)
  laptop: '1024px',
  
  // Tablet
  tablet: '768px',
  
  // Mobile
  mobile: '480px',
  
  // Ultra-wide
  ultraWide: '1920px',
};

/* ==================== COMPONENT SPECIFICATIONS ==================== */

const COMPONENTS = {
  // Buttons
  button: {
    primary: {
      padding: '16px 28px',
      background: 'linear-gradient(135deg, #F5F7FA 0%, #E8EAEE 100%)',
      color: '#050607',
      borderRadius: '12px',
      fontWeight: 700,
      fontSize: '14px',
      border: 'none',
      cursor: 'pointer',
      letterSpacing: '0.5px',
    },
    
    secondary: {
      padding: '6px 12px',
      background: 'rgba(0, 229, 255, 0.1)',
      border: '1px solid #00E5FF',
      color: '#00E5FF',
      borderRadius: '6px',
      fontSize: '12px',
      fontWeight: 600,
      cursor: 'pointer',
    }
  },
  
  // Input Fields
  input: {
    padding: '16px 20px',
    background: 'rgba(5, 6, 7, 0.6)',
    border: '1px solid rgba(0, 229, 255, 0.15)',
    borderRadius: '12px',
    color: '#F5F7FA',
    fontSize: '14px',
    fontFamily: 'inherit',
    transition: 'all 0.3s ease',
  },
  
  // Cards
  card: {
    background: 'linear-gradient(135deg, rgba(15, 17, 21, 0.85) 0%, rgba(18, 20, 25, 0.8) 100%)',
    border: '1px solid rgba(255, 255, 255, 0.06)',
    borderRadius: '20px',
    padding: '32px',
    boxShadow: '0 0 40px rgba(0, 229, 255, 0.08), inset 0 0 40px rgba(0, 229, 255, 0.03)',
    transition: 'all 0.3s cubic-bezier(0.22, 1, 0.36, 1)',
  },
  
  // Badges
  badge: {
    padding: '6px 12px',
    background: 'rgba(15, 17, 21, 0.8)',
    border: '1px solid rgba(255, 255, 255, 0.06)',
    borderRadius: '8px',
    fontSize: '12px',
    fontWeight: 600,
    color: '#9CA3AF',
  },
};

/* ==================== USAGE EXAMPLES ==================== */

/*

// Example: Creating a card with hover effect
const cardStyle = `
  ${COMPONENTS.card.background}
  border: ${COMPONENTS.card.border};
  border-radius: ${COMPONENTS.card.borderRadius};
  padding: ${COMPONENTS.card.padding};
  box-shadow: ${COMPONENTS.card.boxShadow};
  transition: ${ANIMATIONS.transitions.hover};
  
  &:hover {
    border-color: ${COLORS.cyan.primary};
    box-shadow: ${EFFECTS.glows.large};
    transform: translateY(-4px);
  }
`;

// Example: Button styling
const buttonStyle = `
  padding: ${COMPONENTS.button.primary.padding};
  background: ${COMPONENTS.button.primary.background};
  color: ${COMPONENTS.button.primary.color};
  border-radius: ${COMPONENTS.button.primary.borderRadius};
  font-weight: ${COMPONENTS.button.primary.fontWeight};
  font-size: ${COMPONENTS.button.primary.fontSize};
  letter-spacing: ${COMPONENTS.button.primary.letterSpacing};
  box-shadow: ${EFFECTS.glows.small};
  
  &:hover {
    transform: translateY(-2px);
    box-shadow: ${EFFECTS.glows.medium};
  }
`;

// Example: Responsive layout
@media (max-width: ${BREAKPOINTS.laptop}) {
  .sidebar {
    width: 180px;
  }
  .main-content {
    margin-left: 180px;
  }
}

@media (max-width: ${BREAKPOINTS.tablet}) {
  .sidebar {
    width: 60px;
  }
  .main-content {
    margin-left: 60px;
  }
  .module-grid {
    grid-template-columns: 1fr;
  }
}

*/

export { COLORS, TYPOGRAPHY, SPACING, EFFECTS, ANIMATIONS, BREAKPOINTS, COMPONENTS };
