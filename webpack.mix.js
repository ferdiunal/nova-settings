let tailwindcss = require("tailwindcss");
let postcssImport = require("postcss-import");
let postcssRtlcss = require("postcss-rtlcss");
let mix = require("laravel-mix");
const path = require("path");

require("./nova.mix");

mix
  .setPublicPath("dist")
  .js("resources/js/tool.js", "js")
  .vue({ version: 3 })
  .postCss("./resources/css/tool.css", "css", [
    postcssImport(),
    tailwindcss("tailwind.config.js"),
    postcssRtlcss(),
  ])
  .nova("ferdiunal/nova-settings");
