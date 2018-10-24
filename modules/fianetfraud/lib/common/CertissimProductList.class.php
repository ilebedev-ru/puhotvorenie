<?php
/**
 * 2007-2014 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2014 PrestaShop SA
 *  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * objet pour la balise <list>
 *
 * @author ESPIAU Nicolas <nicolas.espiau at fia-net.com>
 */
class CertissimProductList extends CertissimXMLElement
{
    public function __construct($produits = array())
    {
        parent::__construct("<list nbproduit='0'></list>");

        foreach ($produits as $produit) {
            $this->addProduit($produit);
        }
    }

    /**
     * ajoute le produit dans la liste, et incrémente l'attribut nbproduits du nombre de produit ajoutés
     * 
     * @param mixed $produit un tableau ou un objet XMLElement
     * @param array $attrs tableau regroupant les attributs, renseigné si $produit est sous forme de tableau
     * @return XMLElement le produit ajouté
     */
    public function addProduit($produit, $attrs = array())
    {
        $produit = $this->childProduit($produit, $attrs, true);
        $this->addAttribute('nbproduit', $this->getAttribute('nbproduit') + $produit->getAttribute('nb'));

        return $produit;
    }
}