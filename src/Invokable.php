<?php

namespace Poppables;

interface Invokable
{
    public function getCallable(): callable;
}
