<?php

// src/AppMatrix/MatrixBundle/Entity/District.php

namespace AppMatrix\MatrixBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="district")
 */

class District
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
    protected $district_name;

    /**
     * @ORM\Column(type="string")
     */
    protected $district_type;

    /**
     * @ORM\Column(type="string")
     */
    protected $year;

    /**
     * @ORM\Column(type="string")
     */
    protected $parameter_name;

    /**
     * @ORM\Column(type="string")
     */
    protected $parameter_value;

    /**
     * @ORM\Column(type="string")
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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getDistrictName()
    {
        return $this->district_name;
    }

    /**
     * @param mixed $district_name
     */
    public function setDistrictName($district_name)
    {
        $this->district_name = $district_name;
    }

    /**
     * @return mixed
     */
    public function getDistrictType()
    {
        return $this->district_type;
    }

    /**
     * @param mixed $district_type
     */
    public function setDistrictType($district_type)
    {
        $this->district_type = $district_type;
    }

    /**
     * @return mixed
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param mixed $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * @return mixed
     */
    public function getParameterName()
    {
        return $this->parameter_name;
    }

    /**
     * @param mixed $parameter_name
     */
    public function setParameterName($parameter_name)
    {
        $this->parameter_name = $parameter_name;
    }

    /**
     * @return mixed
     */
    public function getParameterValue()
    {
        return $this->parameter_value;
    }

    /**
     * @param mixed $parameter_value
     */
    public function setParameterValue($parameter_value)
    {
        $this->parameter_value = $parameter_value;
    }

    /**
     * @return mixed
     */
    public function getParameterType()
    {
        return $this->parameter_type;
    }

    /**
     * @param mixed $parameter_type
     */
    public function setParameterType($parameter_type)
    {
        $this->parameter_type = $parameter_type;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return mixed
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param mixed $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

}
