<?php

// src/AppMatrix/MatrixBundle/Entity/Parameter.php

namespace AppMatrix\MatrixBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppMatrix\MatrixBundle\Entity\Project;
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
     * @ORM\OneToOne(targetEntity="Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     * @ORM\Column(type="string")
     */
    protected $project_id;

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
     * @return Project
     */
    public function getProjectId()
    {
        return $this->project_id;
    }

    /**
     * @param mixed $project_id
     */
    public function setProjectId(Project $project_id)
    {
        $this->project_id = $project_id;
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
