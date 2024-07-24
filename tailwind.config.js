/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
        backgroundImage: {
            'gacha-result-bg': "url('/storage/public/images/background/T_LuckdrawShare.png')",
        }
    },
  },
  plugins: [
    require('flowbite/plugin')
  ],
}
