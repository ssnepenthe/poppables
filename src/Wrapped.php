<?php

namespace Poppables;

interface Wrapped
{
    public function getCallable(): callable;
}
