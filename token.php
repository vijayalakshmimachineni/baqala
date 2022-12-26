<?php
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => "http://pickdata.in/onlinegrocery/index.php?route=api%2Flogin",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => "key=LXXv9z7Jq7L8VSYClYbV3VqzVi5NlRE0lau3tdTyslsm9HXhjn0zMtYmF4taLTIODxHUMw1HAtrgzaODwHQW2rFT49YMzLIuQ44t5gQimaNT12J4ill4Jts8VZrA92fdWTy21EDZc15h4cuMVDR4BgOADMctjYzHHpG95PxWJpBI8TZ2ufwMCKCjSmiqKZnT9Y6Rm1CEfftUWwTDGLI5bm6Mxcx77FzMspKE6rqHxLqLwkFFMcmage0ud0C8bPQF&username=Default",
    CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/x-www-form-urlencoded",
        "postman-token: 60329eeb-62ad-78e6-f564-486a6a1fe051"
    ),
));
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
if ($err) {
    echo "cURL Error #:" . $err;
} else {
    echo $response;
}