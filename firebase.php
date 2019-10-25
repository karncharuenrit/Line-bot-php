<html>
<?php

use Kreait\Firebase\Factory;

$factory = (new Factory)
    ->withServiceAccount('/path/to/google-service-account.json')
    // The following line is optional if the project id in your credentials file
    // is identical to the subdomain of your Firebase project. If you need it,
    // make sure to replace the URL with the URL of your project.
    ->withDatabaseUri('https://my-project.firebaseio.com');

$database = $factory->createDatabase();
$newPost = $database
    ->getReference('blog/posts')
    ->push([
        'title' => 'Post title',
        'body' => 'This should probably be longer.'
    ]);

$newPost->getKey('AIzaSyBKnAggcRfmdDSGlTorWDZlYLvNGj4TSqQ'); // => -KVr5eu8gcTv7_AHb-3-
$newPost->getUri('https://readsid-5a802.firebaseio.com'); // => https://my-project.firebaseio.com/blog/posts/-KVr5eu8gcTv7_AHb-3-

$newPost->getChild('title')->set('Changed post title');
$newPost->getValue($text); // Fetches the data from the realtime database
$newPost->remove();


?>
<header>
    <!-- The core Firebase JS SDK is always required and must be listed first -->
    <script src="/__/firebase/7.2.2/firebase-app.js"></script>

    <!-- TODO: Add SDKs for Firebase products that you want to use
     https://firebase.google.com/docs/web/setup#available-libraries -->
    <script src="/__/firebase/7.2.2/firebase-analytics.js"></script>

    <!-- Initialize Firebase -->
    <script src="/__/firebase/init.js"></script>
    <script>
        const firebaseConfig = {
            apiKey: "AIzaSyBKnAggcRfmdDSGlTorWDZlYLvNGj4TSqQ",
            authDomain: "readsid-5a802.firebaseapp.com",
            databaseURL: "https://readsid-5a802.firebaseio.com",
            projectId: "readsid-5a802",
            storageBucket: "readsid-5a802.appspot.com",
            messagingSenderId: "722743443476",
            appId: "1:722743443476:web:b9e4efb51297b90103a701",
            measurementId: "G-4FTMTBWCQR"
        };
    </script>
</header>

<body>

</body>

</html>