module.exports = {
  content: [
    './templates/**/*.html.twig',
    './assets/**/*.js',
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}

module.exports = {
  content: [
    "./templates/**/*.html.twig",
    "./assets/**/*.js",
    "./node_modules/preline/dist/*.js",
  ],
  plugins: [
    require('preline/plugin'),
  ],
}