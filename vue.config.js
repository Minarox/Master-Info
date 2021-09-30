const path = require('path');

module.exports = {
  devServer: {
    contentBase: path.join(__dirname, "public/"),
    port: 8080,
    public: 'seqrity.minarox.fr',
    disableHostCheck: false,
  }
}
