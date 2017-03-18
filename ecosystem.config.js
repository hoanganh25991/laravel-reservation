module.exports = {
  /**
   * Application configuration section
   * http://pm2.keymetrics.io/docs/usage/application-declaration/
   */
  apps : [
    // First application
    {
      name        : "queue-jobs",
      script      : "artisan",
      args        : "queue:work",
      interpreter : "php"
    },
  ],
}
