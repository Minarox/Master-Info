const path = require('path');

module.exports = {
  publicPath: process.env.NODE_ENV === 'production'
      ? '/Workshop/'
      : '/',
  devServer: {
    contentBase: path.join(__dirname, "public/"),
    port: 8080
  }
}
