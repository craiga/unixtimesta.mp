const path = require("path");

module.exports = {
  entry: "./scss/main.scss",
  output: {
    filename: "main.css",
    path: path.resolve(__dirname, "unixtimestamp/static"),
  },
  module: {
    rules: [
      {
        test: /\.scss$/,
        use: ["sass-loader"],
      },
    ],
  },
};
