const { createProxyMiddleware } = require('http-proxy-middleware');

module.exports = function(app) {
    app.use(
        '/api',
        createProxyMiddleware({
            target: 'http://wb.cn',
            secure: false,
            changeOrigin: true,
            pathRewrite: {
                "^/api": "/api"
            }
        })
    );
};