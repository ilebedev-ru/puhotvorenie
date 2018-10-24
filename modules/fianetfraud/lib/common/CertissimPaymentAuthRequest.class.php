<?php

/**
 * Global class for the PaymentAuthRequest
 *
 * @author CYRILLE Yann
 */
class CertissimPaymentAuthRequest extends CertissimXMLElement
{
    const VERSION = '1.0';

    public function __construct($id = null)
    {
        parent::__construct('<paymentAuthRequest></paymentAuthRequest>');

        $this->addAttribute('version', self::VERSION);
        $this->addAttribute('id', $id);
    }
}
