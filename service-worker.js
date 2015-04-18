self.addEventListener('push', function(event) {  
  console.log('Received a push message', event);

  var title = 'New Interaction.';  
  var body = 'You have received a new Webmention.';  
  var icon = '/image/static/icon_192.jpg';  
  var tag = 'indieweb-notification';

  event.waitUntil(  
    self.registration.showNotification(title, {  
      body: body,  
      icon: icon,  
      tag: tag  
    })  
  );  
});
