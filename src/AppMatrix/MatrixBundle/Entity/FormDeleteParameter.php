<?php

// src/AppMatrix/MatrixBundle/Entity/Enquiry.php

namespace AppMatrix\MatrixBundle\Entity;


class FormDeleteParameter
{

    protected $yes;

    protected $no;

    /**
     * @return mixed
     */
    public function getYes()
    {
        return $this->yes;
    }

    /**
     * @param mixed $yes
     */
    public function setYes($yes)
    {
        $this->yes = $yes;
    }

    /**
     * @return mixed
     */
    public function getNo()
    {
        return $this->no;
    }

    /**
     * @param mixed $no
     */
    public function setNo($no)
    {
        $this->no = $no;
    }



}