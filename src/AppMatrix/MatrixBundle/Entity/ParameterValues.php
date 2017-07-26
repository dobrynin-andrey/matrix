<?php

// src/AppMatrix/MatrixBundle/Entity/Parameter.php

namespace AppMatrix\MatrixBundle\Entity;
use AppMatrix\MatrixBundle\Entity\District;
use AppMatrix\MatrixBundle\Entity\Project;
use AppMatrix\MatrixBundle\Entity\Parameter;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="parameter_values")
 */

class ParameterValues
{


    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * Many Parameters has One Project.
     *
     * @ORM\ManyToOne(targetEntity="AppMatrix\MatrixBundle\Entity\Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    protected $project;

    /**
     * Many Parameter has One District.
     * @ORM\ManyToOne(targetEntity="AppMatrix\MatrixBundle\Entity\District")
     * @ORM\JoinColumn(name="district_id", referencedColumnName="id")
     */
    protected $district;

    /**
     * Many Values has One Parameter.
     * @ORM\ManyToOne(targetEntity="AppMatrix\MatrixBundle\Entity\Parameter")
     * @ORM\JoinColumn(name="parameter_id", referencedColumnName="id")
     */
    protected $parameter;


    /**
     * @ORM\Column(type="float")
     */
    protected $parameter_value;


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
     * Set parameterValue
     *
     * @param string $parameterValue
     *
     * @return ParameterValues
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
     * Set year
     *
     * @param string $year
     *
     * @return ParameterValues
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
     * @param \DateTime
     *
     * @return ParameterValues
     */
    public function setCreated()
    {
        $this->created = new \DateTime();

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
     * @return ParameterValues
     */
    public function setUpdated()
    {
        $this->updated = new \DateTime();

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
     * @return ParameterValues
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
     * @return ParameterValues
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

    /**
     * Set parameter
     *
     * @param \AppMatrix\MatrixBundle\Entity\Parameter $parameter
     *
     * @return ParameterValues
     */
    public function setParameter(\AppMatrix\MatrixBundle\Entity\Parameter $parameter = null)
    {
        $this->parameter = $parameter;

        return $this;
    }

    /**
     * Get parameter
     *
     * @return \AppMatrix\MatrixBundle\Entity\Parameter
     */
    public function getParameter()
    {
        return $this->parameter;
    }
}
