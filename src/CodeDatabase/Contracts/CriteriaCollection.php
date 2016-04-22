<?php

namespace CodePress\CodeDatabase\Contracts;

interface CriteriaCollection
{

    public function addCriteria(CriteriaInterface $criteriaInterface);

    public function getCriteriaCollection();

    public function getByCriteria(CriteriaInterface $criteriaInterface);

    public function applyCriteria();

    public function ignoreCriteria($isIgnored = true);
    
    public function clearCriteria();
}
