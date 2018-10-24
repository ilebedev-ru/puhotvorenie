<?php

/**
 * Objet XMLElement <validstack> de retour des script d'envoi de transaction à Certissim (stacking)
 *
 * @version 3.1
 * @author ESPIAU Nicolas
 */
class CertissimPaymentAuthResponse extends CertissimXMLResult
{
    const ROOT_NAME = "paymentAuthResponse";

    public function __construct($data)
    {
        $data = preg_replace('#\"#', '\'', $data);
        parent::__construct($data);

        if ($this->getName() != self::ROOT_NAME) {
            $msg = "L'élément racine n'est pas valide : " . $this->getName() . " trouvé, " . self::ROOT_NAME . " attendu.";
            insertLog(__FILE__ . " - __construct()", $msg);
        }
    }

    /**
     * retourne vrai si le stack de transaction a été refusé, faux sinon
     *
     * @return bool
     */
    public function hasFatalError()
    {
        return count($this->getChildrenByName('unluck'))>0;
    }

    /**
     * retourne le libellé de l'erreur si <unluck>, null sinon
     *
     * @return mixed
     */
    public function getError()
    {
        $unluck = $this->hasFatalError() ? array_pop($this->getChildrenByName('unluck'))->getValue() : null;

        return ($unluck);
    }

    /**
     * retourne un tableau contenant tous les éléments <result> sous forme d'objets SendStackResult
     *
     * @return array
     */
    public function getResults()
    {
        $results = array();
        foreach ($this->getChildrenByName('authLevel') as $result) {
            $results[] = new ValidstackResultResponse($result->getXML());
        }

        return $results;
    }

    public function getResultCount()
    {
        return count($this->$this->getChildrenByName('authLevel'));
    }
    
    public function getAuthLevel()
    {
        $authlevel = $this->getChildrenByName('authLevel');
        $authlevel = array_pop($authlevel);
        return $authlevel->getValue();
    }
    
    public function getAuthId()
    {
        return $this->getAttribute('id');
    }
}
