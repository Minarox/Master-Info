const path = require("path");

module.exports = {
  devServer: {
    contentBase: path.join(__dirname, "public/"),
    port: 8200,
    public: "ic.minarox.fr",
    disableHostCheck: true,
  }
};
