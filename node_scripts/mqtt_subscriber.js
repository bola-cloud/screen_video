const mqtt = require('mqtt');
const { exec } = require('child_process');
const client = mqtt.connect('mqtt://77.37.54.128:1883'); // MQTT Broker IP

client.on('connect', function () {
    console.log('Subscribed to video_end events');

    // Subscribe to all TVs' video_end topics (e.g., tv/1/video_end)
    client.subscribe('tv/+/video_end');
});

client.on('message', function (topic, message) {
    const tvId = topic.split('/')[1]; // Extract tv_id from the topic
    console.log(`Video end received from TV ${tvId}`);

    // When a video ends, trigger the next ad by calling the Laravel endpoint
    exec(`php artisan publish:next-ad ${tvId}`, (err, stdout, stderr) => {
        if (err) {
            console.error(`Error executing publish-next-ad: ${stderr}`);
            return;
        }
        console.log(`Next ad published for TV ${tvId}: ${stdout}`);
    });
});
