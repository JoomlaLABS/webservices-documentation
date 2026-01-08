/**
 * Redoc Themes Configuration
 * Light and Dark themes for Redoc documentation
 */

const RedocThemes = {
  light: {
    spacing: {
      unit: 5,
      sectionHorizontal: 40,
      sectionVertical: 40,
    },
    breakpoints: {
      small: '50rem',
      medium: '85rem',
      large: '105rem',
    },
    colors: {
      tonalOffset: 0.3,
      primary: {
        main: '#1976d2',
      },
      success: {
        main: '#22c55e',
      },
      warning: {
        main: '#eab308',
      },
      error: {
        main: '#ef4444',
      },
      border: {
        light: '#e5e7eb',
        dark: '#9ca3af',
      },
      text: {
        primary: '#1f2937',
        secondary: '#6b7280',
        light: '#f9fafb',
      },
      http: {
        get: '#22c55e',
        post: '#3b82f6',
        put: '#ec4899',
        options: '#eab308',
        patch: '#f97316',
        delete: '#ef4444',
        basic: '#71717a',
        link: '#06b6d4',
        head: '#d946ef',
      },
      responses: {
        success: {
          color: '#22c55e',
          backgroundColor: 'rgba(34,197,94,0.1)',
          borderColor: '#86efac',
          tabTextColor: '#22c55e',
        },
        error: {
          color: '#ef4444',
          backgroundColor: 'rgba(239,68,68,0.1)',
          borderColor: '#fca5a5',
          tabTextColor: '#ef4444',
        },
        redirect: {
          color: '#eab308',
          backgroundColor: 'rgba(234,179,8,0.1)',
          borderColor: '#fde047',
          tabTextColor: '#eab308',
        },
        info: {
          color: '#3b82f6',
          backgroundColor: 'rgba(59,131,246,0.1)',
          borderColor: '#93c5fd',
          tabTextColor: '#3b82f6',
        },
      },
    },
    typography: {
      fontSize: '14px',
      lineHeight: '1.5em',
      fontWeightRegular: '400',
      fontWeightBold: '600',
      fontWeightLight: '300',
      fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
      smoothing: 'antialiased',
      optimizeSpeed: true,
      headings: {
        fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
        fontWeight: '600',
        lineHeight: '1.6em',
      },
      code: {
        fontSize: '13px',
        fontFamily: 'Consolas, Monaco, "Courier New", monospace',
        color: '#e11d48',
        backgroundColor: 'rgba(249, 250, 251, 0.8)',
        wrap: false,
      },
      links: {
        color: '#1976d2',
        visited: '#1976d2',
        hover: '#0ea5e9',
        textDecoration: 'none',
        hoverTextDecoration: 'underline',
      },
    },
    sidebar: {
      width: '260px',
      backgroundColor: '#f9fafb',
      textColor: '#374151',
      activeTextColor: '#1976d2',
      groupItems: {
        textTransform: 'uppercase',
      },
      level1Items: {
        textTransform: 'none',
      },
      arrow: {
        size: '1.5em',
      },
    },
    logo: {
      gutter: '2px',
    },
    rightPanel: {
      backgroundColor: '#1f2937',
      width: '40%',
      textColor: '#ffffff',
      servers: {
        overlay: {
          backgroundColor: '#f9fafb',
          textColor: '#1f2937',
        },
        url: {
          backgroundColor: '#ffffff',
        },
      },
    },
    codeBlock: {
      backgroundColor: '#1f2937',
    },
    fab: {
      backgroundColor: '#1976d2',
      color: '#ffffff',
    },
  },
  
  dark: {
    spacing: {
      unit: 5,
      sectionHorizontal: 40,
      sectionVertical: 40,
    },
    breakpoints: {
      small: '50rem',
      medium: '85rem',
      large: '105rem',
    },
    colors: {
      tonalOffset: 0.3,
      primary: {
        main: '#71717a',
      },
      success: {
        main: '#22c55e',
      },
      warning: {
        main: '#eab308',
      },
      error: {
        main: '#ef4444',
      },
      border: {
        light: '#27272a',
        dark: '#a1a1aa',
      },
      text: {
        primary: '#fafafa',
        secondary: '#d4d4d8',
        light: '#3f3f46',
      },
      http: {
        get: '#22c55e',
        post: '#3b82f6',
        put: '#ec4899',
        options: '#eab308',
        patch: '#f97316',
        delete: '#ef4444',
        basic: '#71717a',
        link: '#06b6d4',
        head: '#d946ef',
      },
      responses: {
        success: {
          color: '#22c55e',
          backgroundColor: 'rgba(34,197,94,0.1)',
          borderColor: '#86efac',
          tabTextColor: '#22c55e',
        },
        error: {
          color: '#ef4444',
          backgroundColor: 'rgba(239,68,68,0.1)',
          borderColor: '#fca5a5',
          tabTextColor: '#ef4444',
        },
        redirect: {
          color: '#eab308',
          backgroundColor: 'rgba(234,179,8,0.1)',
          borderColor: '#fde047',
          tabTextColor: '#eab308',
        },
        info: {
          color: '#3b82f6',
          backgroundColor: 'rgba(59,131,246,0.1)',
          borderColor: '#93c5fd',
          tabTextColor: '#3b82f6',
        },
      },
    },
    typography: {
      fontSize: '14px',
      lineHeight: '1.5em',
      fontWeightRegular: '400',
      fontWeightBold: '600',
      fontWeightLight: '300',
      fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
      smoothing: 'antialiased',
      optimizeSpeed: true,
      headings: {
        fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
        fontWeight: '600',
        lineHeight: '1.6em',
      },
      code: {
        fontSize: '13px',
        fontFamily: 'Consolas, Monaco, "Courier New", monospace',
        color: '#fde047',
        backgroundColor: '#18181b',
        wrap: false,
      },
      links: {
        color: '#0ea5e9',
        visited: '#0ea5e9',
        hover: '#0ea5e9',
        textDecoration: 'none',
        hoverTextDecoration: 'underline',
      },
    },
    sidebar: {
      width: '260px',
      backgroundColor: '#18181b',
      textColor: '#a1a1aa',
      activeTextColor: '#ffffff',
      groupItems: {
        textTransform: 'uppercase',
      },
      level1Items: {
        textTransform: 'none',
      },
      arrow: {
        size: '1.5em',
      },
    },
    logo: {
      gutter: '2px',
    },
    rightPanel: {
      backgroundColor: '#27272a',
      width: '40%',
      textColor: '#ffffff',
      servers: {
        overlay: {
          backgroundColor: '#27272a',
        },
        url: {
          backgroundColor: '#18181b',
        },
      },
    },
    codeBlock: {
      backgroundColor: '#18181b',
    },
    fab: {
      backgroundColor: '#52525b',
      color: '#67e8f9',
    },
    schema: {
      linesColor: '#d8b4fe',
      typeNameColor: '#93c5fd',
      typeTitleColor: '#1d4ed8',
    },
    extensionsHook: (c) => {
      if (c === 'UnderlinedHeader') {
        return {
          color: '#a1a1aa',
          fontWeight: 'bold',
          borderBottom: '1px solid #3f3f46',
        };
      }
    },
  },
};

// Export for use in other scripts
if (typeof window !== 'undefined') {
  window.RedocThemes = RedocThemes;
}
