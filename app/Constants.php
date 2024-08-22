<?php

namespace App;

final class Constants
{
    const bool TEST_MODE = false;
    const string TEST_MESSAGE = "## Test Message\n\nThis is a **test** message with some *italic* text and a [link](https://google.com).";

    const bool RELATED_QUESTIONS_ENABLED = true;

    const string CHATBUDDY_SELECTED_LLM_KEY = 'ChatBuddy-LLM';
    const string CHATBUDDY_LOADING_STRING = 'Thinking...';
    const int CHATBUDDY_TOTAL_CONVERSATION_HISTORY = 50;

    const string TEXTSTYLER_SELECTED_LLM_KEY = 'TextStyler-LLM';

    const string OPENAI_EMBEDDING_MODEL = 'text-embedding-ada-002';
    const string GEMINI_EMBEDDING_MODEL = 'embedding-001';
    const string OLLAMA_EMBEDDING_MODEL = 'mistral:latest';
}
