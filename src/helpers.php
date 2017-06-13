<?php
/**
 * Create a record from the given value.
 *
 * @param  mixed  $value
 * @return \STS\Record\Record
 */
function record($value = null)
{
    return new \STS\Record\Record($value);
}