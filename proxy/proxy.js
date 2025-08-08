const express = require('express');
const { createProxyMiddleware } = require('http-proxy-middleware');

const app = express();

app.use('/v1', createProxyMiddleware({
  target: 'https://webexapis.com',
  changeOrigin: true,
  // Add any additional options if needed
}));

app.use(function (req, res, next) {
    res.header('Access-Control-Allow-Origin', 'http://chd1.webex.org'); // Set the correct origin
    res.header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    res.header('Access-Control-Allow-Headers', 'Origin, Content-Type, X-CSRF-Token');
    res.header('Access-Control-Allow-Credentials', true);
    next();
  });

app.listen(3000, () => {
  console.log('Proxy server listening on port 3000');
});