const path = require("path");

module.exports = {
  devServer: {
    contentBase: path.join(__dirname, "public/"),
    port: 8101,
    public: "mspr.minarox.fr",
    disableHostCheck: true,
  },
};
