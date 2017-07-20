<?php

// src/AppMatrix/MatrixBundle/Entity/Enquiry.php

namespace AppMatrix\MatrixBundle\Entity;


class FormProject
{

    protected $project_name;

    /**
     * @return mixed
     */
    public function getProjectName()
    {
        return $this->project_name;
    }

    /**
     * @param mixed $project_name
     */
    public function setProjectName($project_name)
    {
        $this->project_name = $project_name;
    }



}