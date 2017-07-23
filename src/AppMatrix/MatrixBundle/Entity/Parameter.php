<?php

// src/AppMatrix/MatrixBundle/Entity/Parameter.php

namespace AppMatrix\MatrixBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use AppMatrix\MatrixBundle\Entity\ParameterType;

/**
 * @ORM\Entity
 * @ORM\Table(name="parameter")
 */

class Parameter
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $parameter_name;


    /**
     * Many ParameterType has One Parameter.
     * @ORM\ManyToOne(targetEntity="AppMatrix\MatrixBundle\Entity\ParameterType")
     * @ORM\JoinColumn(name="parameter_type_id", referencedColumnName="id")
     */
    protected $parameter_type;


    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set parameterName
     *
     * @param string $parameterName
     *
     * @return Parameter
     */
    public function setParameterName($parameterName)
    {
        $this->parameter_name = $parameterName;

        return $this;
    }

    /**
     * Get parameterName
     *
     * @return string
     */
    public function getParameterName()
    {
        return $this->parameter_name;
    }

    /**
     * Set created
     *
     * @param \DateTime
     *
     * @return Parameter
     */
    public function setCreated()
    {
        $this->created = new \DateTime;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime
     *
     * @return Parameter
     */
    public function setUpdated()
    {
        $this->updated = new \DateTime;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set parameterType
     *
     * @param \AppMatrix\MatrixBundle\Entity\ParameterType $parameterType
     *
     * @return Parameter
     */
    public function setParameterType(\AppMatrix\MatrixBundle\Entity\ParameterType $parameterType = null)
    {
        $this->parameter_type = $parameterType;

        return $this;
    }

    /**
     * Get parameterType
     *
     * @return \AppMatrix\MatrixBundle\Entity\ParameterType
     */
    public function getParameterType()
    {
        return $this->parameter_type;
    }

}
