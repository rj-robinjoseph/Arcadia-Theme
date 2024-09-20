const path = require("path");
const fs = require("fs");
const {
    setup,
    styles,
    javascript,
    browsersync,
    images,
    monitor,
} = require("argonauts");
const { parallel, series } = require("gulp");

setup({
    name: "Arcadia",
    url: "http://lac-seul-orc-3.local/",
    entries: ["main"],
    dest: "",
    css: {
        in: "scss",
        out: ".",
    },
    append: (filename) => {
        const basename = path.basename(filename, ".ttf");
        fs.appendFile(
            "src/scss/base/_font_families.scss",
            `@include fontface("${basename}", "${basename}");\n`,
            () => {}
        );
    },
    watch: ["*.php", "**/*.php", "updated.txt"],
});

exports.init = parallel(styles, javascript);
exports.default = series(
    browsersync,
    parallel(styles, javascript, images),
    monitor
);
