<?php

// src/AppMatrix/MatrixBundle/Entity/Parameter.php

namespace AppMatrix\MatrixBundle\Entity;
use AppMatrix\MatrixBundle\Entity\District;
use AppMatrix\MatrixBundle\Entity\Project;

use Doctrine\ORM\Mapping as ORM;

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
     * One Parameter has One Project.
     *
     * @ORM\ManyToOne(targetEntity="AppMatrix\MatrixBundle\Entity\Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    protected $project;

    /**
     * One Parameter has One District.
     * @ORM\ManyToOne(targetEntity="AppMatrix\MatrixBundle\Entity\District")
     * @ORM\JoinColumn(name="district_id", referencedColumnName="id")
     */
    protected $district;

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
     * @ORM\Column(type="string")
     */
    protected $year;

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
     * Set parameterValue
     *
     * @param string $parameterValue
     *
     * @return Parameter
     */
    public function setParameterValue($parameterValue)
    {
        $this->parameter_value = $parameterValue;

        return $this;
    }

    /**
     * Get parameterValue
     *
     * @return string
     */
    public function getParameterValue()
    {
        return $this->parameter_value;
    }

    /**
     * Set parameterType
     *
     * @param string $parameterType
     *
     * @return Parameter
     */
    public function setParameterType($parameterType)
    {
        $this->parameter_type = $parameterType;

        return $this;
    }

    /**
     * Get parameterType
     *
     * @return string
     */
    public function getParameterType()
    {
        return $this->parameter_type;
    }

    /**
     * Set year
     *
     * @param string $year
     *
     * @return Parameter
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return string
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Parameter
     */
    public function setCreated($created)
    {
        $this->created = $created;

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
     * @param \DateTime $updated
     *
     * @return Parameter
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

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
     * Set project
     *
     * @param \AppMatrix\MatrixBundle\Entity\Project $project
     *
     * @return Parameter
     */
    public function setProject(\AppMatrix\MatrixBundle\Entity\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \AppMatrix\MatrixBundle\Entity\Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set district
     *
     * @param \AppMatrix\MatrixBundle\Entity\District $district
     *
     * @return Parameter
     */
    public function setDistrict(\AppMatrix\MatrixBundle\Entity\District $district = null)
    {
        $this->district = $district;

        return $this;
    }

    /**
     * Get district
     *
     * @return \AppMatrix\MatrixBundle\Entity\District
     */
    public function getDistrict()
    {
        return $this->district;
    }
}
