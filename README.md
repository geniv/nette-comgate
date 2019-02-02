Comgate
=======

Comgate payment gateway

inspired: https://github.com/renat-magadiev/comgate-client , https://github.com/LZaplata/Comgate

comgate info pdf: https://www.comgate.cz/files/informacni-brozura-comgate-payments.pdf

list test payment:
https://portal.comgate.cz/cs/testovaci-platby

api:
https://platebnibrana.comgate.cz/cz/protokol-api

need set in:

https://portal.comgate.cz/cs/propojeni-obchodu-detail/id/**XXmerchantIdXX**
- Url zaplacený
- Url zrušený
- Url nevyřízený
- Url pro předání výsledku platby
- Povolené IP adresy

***

thanks for code review: @AdamSmid

***

example url for production:
---
redirect url (GET redirect from Comgate):
`https://example.cz/summary/result/${id}?refId=${refId}`

status url (POST check from Comgate):
`https://example.cz/summary/status`


Installation
------------

```sh
$ composer require geniv/nette-comgate
```
or
```json
"geniv/nette-comgate": ">=1.0.0"
```

require:
```json
"php": ">=7.0.0",
"nette/nette": ">=2.4.0",
"renat-magadiev/comgate-client": "^1.0"
```

Include in application
----------------------

neon configure:
```neon
# comgate
comgate:
    merchantId: "xxxyyy"
    secret: "******"
    sandbox: true
```

neon configure extension:
```neon
extensions:
    comgate: Comgate\Bridges\Nette\Extension
```


presenter
create payment:
```php
/** @var Comgate @inject */
public $comgate;
```

create payment:
```php
try {
    // process Comgate
    $payment = $this->comgate->createPayment($values['order_price'] * 100, $values['order_id'], $values['email'], $game['name']);
    $payment->setPrepareOnly(true);

    $response = $this->comgate->sendResponse($payment);
    if ($response->isOk()) {
        // edit checkout_id
        $this->model->editItem($id, ['checkout_id' => $response->getTransId()]);
        $this->redirectUrl($response->getRedirectUrl());
    }
} catch (\Comgate\Exception\InvalidArgumentException $e) {
    Debugger::log($e);
    $this->redirect('error');
} catch (\Comgate\Exception\LabelTooLongException $e) {
    Debugger::log($e);
    $this->redirect('error');
}
```

status payment:
```php
public function actionStatus()
{
    $this->getHttpResponse()->setContentType('application/javascript');
    $request = $this->getHttpRequest();
    if ($request->isMethod('POST')) {
        $transId = $request->getPost('transId');
        $status = $request->getPost('status');
        if ($transId && $status) {
            $item = $this->model->getByCheckoutId($transId);
            if ($item) {
                if ($this->model->editItem((int) $item['id'], ['status' => $status, 'checkout_date%sql' => 'NOW()', 'active' => ($status == PaymentStatus::PAID)])) {
                    $this->sendResponse(new ComgateResponse('code=0&message=OK'));
                }
                $this->sendResponse(new ComgateResponse('code=1&message=SAVE_ERROR'));
            }
            $this->sendResponse(new ComgateResponse('code=1&message=TRANS_ID_NOT_FOUND'));
        }
        $this->sendResponse(new ComgateResponse('code=1&message=MISSING_PARAMETERS'));
    }
    $this->sendResponse(new ComgateResponse('code=1&message=FAIL'));
}
```

result payment:
```php
public function actionResult(string $id, string $refId)
{
    $item = $this->model->getByCheckoutId($id);
    if ($item) {
        if ($item['status'] == PaymentStatus::PAID) {
            //success
        } else {
            //danger
        }
    }
    $this->redirect('Homepage:');
}
```
