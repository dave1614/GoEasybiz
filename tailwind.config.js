/* eslint-env node */

const plugin = require("tailwindcss/plugin");

module.exports = {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.vue',
    './resources/js/**/*.js',
  ],
  darkMode: "class", // or 'media' or 'class'
  theme: {
    
    asideScrollbars: {
      light: "light",
      gray: "gray",
    },
    extend: {
      fontFamily: {
        montserrat: ['Montserrat']
      },
      colors: {
        'facebook-blue': '#3f5e9f',
        'primary': '#00617C',
        'secondary': '#00EBBF',
        'tertiary': '#10c8e3',
        blueGray: {
          50: "rgba(241, 245, 249, 1)",
          100: "rgba(241, 245, 249,1)",
          200: "#e2e8f0",
          300: "#cbd5e1d9",
          400: "#94a3b8",
          500: "#64748b",
          600: "#475569",
          700: "#334155",
          800: "#1e293b",
        }, 
      },
      backgroundImage: {
        'login-background': "linear-gradient(to right bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.2)), url('/public/img/login_background.jpg')",
        'main-background': "linear-gradient(to right bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.2)), url('/public/img/eb896c5e-b5fa-4f68-8cd1-95e32eea93ae.jpg')",
        
        'login-right-background': "url('/public/img/login_right_background.svg')",
        // 'footer-texture': "url('/img/footer-texture.png')",
      },
      zIndex: {
        "-1": "-1",
      },
      flexGrow: {
        5: "5",
      },
      maxHeight: {
        "screen-menu": "calc(100vh - 3.5rem)",
        modal: "calc(100vh - 160px)",
      },
      transitionProperty: {
        position: "right, left, top, bottom, margin, padding",
        textColor: "color",
      },
      keyframes: {
        "fade-out": {
          from: { opacity: 1 },
          to: { opacity: 0 },
        },
        "fade-in": {
          from: { opacity: 0 },
          to: { opacity: 1 },
        },
      },
      animation: {
        "fade-out": "fade-out 250ms ease-in-out",
        "fade-in": "fade-in 250ms ease-in-out",
      },
    },
  },
  plugins: [
    require("@tailwindcss/forms"),
    plugin(function ({ matchUtilities, theme }) {
      matchUtilities(
        {
          "aside-scrollbars": (value) => {
            const track = value === "light" ? "100" : "900";
            const thumb = value === "light" ? "300" : "600";
            const color = value === "light" ? "gray" : value;

            return {
              scrollbarWidth: "thin",
              scrollbarColor: `${theme(`colors.${color}.${thumb}`)} ${theme(
                `colors.${color}.${track}`
              )}`,
              "&::-webkit-scrollbar": {
                width: "8px",
                height: "8px",
              },
              "&::-webkit-scrollbar-track": {
                backgroundColor: theme(`colors.${color}.${track}`),
              },
              "&::-webkit-scrollbar-thumb": {
                borderRadius: "0.25rem",
                backgroundColor: theme(`colors.${color}.${thumb}`),
              },
            };
          },
        },
        { values: theme("asideScrollbars") }
      );
    }),
    require("@tailwindcss/line-clamp"),
  ],
};
