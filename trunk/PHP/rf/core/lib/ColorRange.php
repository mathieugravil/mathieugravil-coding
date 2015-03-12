<?php

class ColorRange {

    public function setKeyPoints ($keyPoints, $colors = array())
    {
        if($this->keyPointsSet)
        {
            RFAssert::Exception('The key points have already been set');
        }
        $this->keyPointsSet = true;

        if(count($colors) > 0)
        {
            // not auto colors
            if(count($keyPoints) - 1 !== count($colors))
            {
                RFAssert::Exception("The key points require ".(count($keyPoints) - 1)." colors. The number of colors given is ".count($colors));
            }
        }
        $this->keyPoints = array(
            'keyPoints' => $keyPoints,
            'colors' => $colors
        );
    }

    public function setPaletteScheme($schemeName)
    {
        RFAssert::InArray("The color scheme name must be valid. ", $schemeName, array('redtogreen', 'shades', 'redtogreenvalue', 'tones', 'greentored', 'greentoredvalue'));
        $this->scheme = $schemeName;
    }

    public function setKeyPercentages ($keyPercentages, $colors = array())
    {
        if($this->keyPointsSet)
        {
            RFAssert::Exception('The key points have already been set');
        }
        $this->keyPointsSet = true;

        if(count($colors) > 0)
        {
            // not auto colors
            if(count($keyPercentages) - 1 !== count($colors))
            {
                RFAssert::Exception("The key points require ".(count($keyPercentages) - 1)." colors. The number of colors given is ".count($colors));
            }
        }

        $this->keyPoints = array(
            'keyPercentages' => $keyPercentages,
            'colors' => $colors
        );
    }

    protected $keyPointsSet = false;

    protected $keyPoints = array();

    protected $scheme = "tones";

    public function toString ()
    {
        return json_encode($this->asArray());
    }

    public function asArray()
    {
        $this->keyPoints['scheme'] = $this->scheme;
        return $this->keyPoints;
    }
}

