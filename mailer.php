<?php

class Api
{
    public $api_url = 'https://cashsmm.com/api/v2';
    public $api_key = '85ef2a4cbc4ce59434d396269c538c7e';

    public function cancel(array $orderIds)
    {
        return json_decode(
            $this->connect([
                'key'    => $this->api_key,
                'action' => 'cancel',
                'orders' => implode(',', $orderIds),
            ]),
            true
        );
    }

    private function connect($post)
    {
        $_post = [];
        foreach ($post as $name => $value) {
            $_post[] = $name . '=' . urlencode($value);
        }

        $ch = curl_init($this->api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, join('&', $_post));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $result = curl_exec($ch);
        if (curl_errno($ch) && empty($result)) {
            $result = false;
        }
        curl_close($ch);
        return $result;
    }
}

/* ---- Test Cancel Single Order ---- */

$api = new Api();
$api->api_key = '85ef2a4cbc4ce59434d396269c538c7e';

// One order ID
$orderId = 968045;

$response = $api->cancel([$orderId]);

echo "<pre>";
print_r($response);
echo "</pre>";

