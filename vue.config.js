const path = require("path");

module.exports = {
  devServer: {
    contentBase: path.join(__dirname, "public/"),
    port: 8100,
    public: "mspr.minarox.fr",
    disableHostCheck: true,
  }
};
