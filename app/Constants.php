<?php

namespace App;

final class Constants
{
    const bool TEST_MODE = false;
    const string TEST_MESSAGE = "## Test Message\n\nThis is a **test** message with some *italic* text and a [link](https://google.com).";

    const string AI_ERROR_MESSSAGE = '<span class="text-red-600">Oops! Failed to get a response, please try again.</span>';

    const string CHATBUDDY_SELECTED_LLM_KEY = 'ChatBuddy-LLM';
    const string CHATBUDDY_LOADING_STRING = 'Thinking...';
    const int CHATBUDDY_TOTAL_CONVERSATION_HISTORY = 50;

    const string TEXTSTYLER_SELECTED_LLM_KEY = 'TextStyler-LLM';
}
