<?php

// src/AppMatrix/MatrixBundle/Entity/Enquiry.php

namespace AppMatrix\MatrixBundle\Entity;


class Form
{
    protected $project_id;

    protected $parameter_type;

    protected $parameter_name;

    protected $district_type;

    protected $file;

    /**
     * @return mixed
     */
    public function getProjectId()
    {
        return $this->project_id;
    }

    /**
     * @param mixed $project_id
     */
    public function setProjectId($project_id)
    {
        $this->project_id = $project_id;
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
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }



}