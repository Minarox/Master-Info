const path = require('path');

module.exports = {
  devServer: {
    contentBase: path.join(__dirname, "public/"),
    port: 8080,
    public: 'workshop.minarox.fr',
    disableHostCheck: false,
  }
}
