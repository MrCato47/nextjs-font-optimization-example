<?php
// api.php - Proxy to fetch products from external API with authorization token

header('Content-Type: application/json');

$apiUrl = "https://developers.syscom.mx/api/v1/productos?marca=ugreen";

$token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjM5YzcxODllNzhjOWUxOGQ0NGE3MWZmYWY4ZGZiMDNlNTBkNDNiYWZiNDZhZGUzZjk0ZDRlMmMzMWFmM2I0NGUwYWFlNDcyYzAyNzgyZDNkIn0.eyJhdWQiOiJ2NVZZN3N4WlZWOUtrbHNiQ0RKQjJqN3RaZzA5emZpdyIsImp0aSI6IjM5YzcxODllNzhjOWUxOGQ0NGE3MWZmYWY4ZGZiMDNlNTBkNDNiYWZiNDZhZGUzZjk0ZDRlMmMzMWFmM2I0NGUwYWFlNDcyYzAyNzgyZDNkIiwiaWF0IjoxNzQ4MDQzNzc3LCJuYmYiOjE3NDgwNDM3NzcsImV4cCI6MTc3OTU3OTc3Niwic3ViIjoiIiwic2NvcGVzIjpbXX0.pL9_7EtxiQYWp2MKDRLYem3A6VpSDZgGh4pmRVq-GOZ5Kes6B9By1sSFUMKwjn-fEbL7So21CTiooU1eadg84gZbAbTLaM7kTEB_tqrkMMqZiZnuRHAm_UuSnnsSsNKDDOeZGeCYtYBdjaMCeaxhTm9l9F2RKAJtV9hNfSvhv9AtAn9ZT7Va8Wj-vDBBvl_dmr4OGao_asauh0BsKqcV_OeEiGR1cw3akKpYE0xNLyBS5FlQxXkh3gm_Wre-dyTHQFFqn2eYiNler_wbuzehjY4Ax9hSzp0dwuVWf81zHqHvaaIs1y973jfG00EOpUUzNMs1StNwTtMjLWhmiFAWOuOTcESjaYgJidxuRwxWOcDmabyO6TqDGEVAOJE2OUaGzPK5yPCcgc6Pkq1yjX_U6tHJhMoY0EZr_OVuUEWWD3HiHysEZ19mb-DsJF964vYReisUhpGHTLUCI9c4MyvKMBu3lsxnxbvJIIUmLDtEpjY66Z-B_fzBjm8BzjpGIL9OYpRm-iTzBnqwGmk3W5J116ThjSoMX-WW9dZcZ2PHnnMeAtza93mxmCWAYURaUMLo8DZQhNa_HZqORg8FcqaiCeNJ1asOgDzump4nXacbGbJB8uivlZvLeb0c7oFstAWFAZgJIffaJn4fO2wuFX9cWKBjCmttclCMMAPsvHrkl4I";

$headers = [
    "Authorization: Bearer $token"
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);

if(curl_errno($ch)){
    http_response_code(500);
    echo json_encode(['error' => curl_error($ch)]);
    curl_close($ch);
    exit;
}

curl_close($ch);

echo $response;
?>
