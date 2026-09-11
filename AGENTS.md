# AI Customer Service Chat Flow

## Overview

The chatbox on the frontend is backed by an AI service that uses OpenAI's function-calling API. It can answer customer questions, look up product stock/availability by brand or model, check an order status, and collect a phone number for a human callback.

## Components

- **Chatbox UI** (`resources/views/layouts/app.blade.php`, `public/css/whatsapp.css`)
- **Chat API** (`routes/api.php`, `app/Http/Controllers/AiController.php`)
- **AI Service** (`app/Services/AiServices.php`)
- **AI Tool Service** (`app/Services/AiToolServices.php`)
- **Conversations / Messages tables** (`chat_conversations`, `chat_messages`)
- **Admin Chat History** (`app/Http/Controllers/ChatHistoryController.php`, `resources/views/admin/chat-history/`)
- **Admin Enquiries** (`app/Http/Controllers/EnquiryController.php`, `resources/views/admin/enquiries/`)

## Interaction Flow

1. **Visitor opens chatbox**
   - User is asked for `name` and `email`.
   - After clicking **Start chat**, the pre-chat form is hidden and the conversation area is shown.

2. **Visitor sends a message**
   - Chatbox posts `POST /api/chat/message` with:
     - `message`
     - `name`
     - `email`
   - The route lives in `routes/api.php` and uses `web` middleware so session and CSRF protection still work.
   - The request includes the Laravel CSRF token.

3. **Controller handles the request** (`AiController::chat`)
   - Validates the payload.
   - Finds or creates a `ChatConversation` keyed by `session_id`, `name`, and `email`.
   - Stores the user's message in `chat_messages` with `role = user`.
   - Calls `AiServices::reply($message, $conversation)` to get an AI response.
   - Stores the agent's message in `chat_messages` with `role = agent`.
   - Updates `last_message_at` on the conversation.
   - Returns `{'message': '...'}` as JSON.

4. **AI Service calls OpenAI** (`AiServices::reply`)
   - Builds a chat request with a system prompt and a list of available tools.
   - Sends the request to OpenAI (`chat.completions`).
   - If the previous agent turn asked for a phone number and the user replies with one, the phone number is saved to the conversation before OpenAI is called.

5. **OpenAI may call a tool**
   - `get_product_stock` – for questions about stock or quantity of a single product.
   - `search_products` – for questions about whether a product is available.
   - `get_order_status` – for questions about an order status.
   - `get_typeof_products` – lists all smartphone brands.
   - `get_modelfrom_type` – lists all smartphone models for a brand.
   - `get_stock_by_type` – lists stock/availability for all models in a brand.
   - `update_phone` – saves a phone number when the customer provides it for a callback.

6. **Tool Service looks up the product** (`AiToolServices`)
   - Product search runs in this order:
     1. Numeric ID match (`Product::find`).
     2. Exact `model` match.
     3. Any `model` contained in the user's text.
   - Returns a string such as:
     - "Product iPhone 15 is in stock, quantity: 500"
     - "Product iPhone 15 is available, quantity: 500"
     - "product out of stock"
     - "Product not found or not available."

7. **AI Service sends the tool result back to OpenAI**
   - The final reply is generated from the tool result.

8. **Final response is returned and displayed**
   - `AiController` sends the message to the chatbox.
   - The chatbox appends it to the conversation.

## Phone Callback Flow

- If the customer asks for information the chatbot does not have, or asks to speak to a person, the chatbot asks for a phone number.
- When a phone number is provided, the `update_phone` tool saves it to the `chat_conversations.phone` column.
- The chatbot replies: "Thank you. Our executive will contact you shortly. Is there anything else you need help with?"
- If the input is not a valid phone number (less than 7 digits), the chatbot asks again with "Invalid phone number. Please enter a valid phone number."

## Admin

- `/admin/chat-history` lists all chat conversations with message counts and latest message times.
- `/admin/chat-history/{id}` shows the full conversation messages.
- `/admin/enquiries` lists only conversations where the customer provided a phone number (`phone IS NOT NULL`), showing **Name**, **Email**, **Phone**, and **Date**.

## How to Configure

Add the OpenAI key to `.env`:

```env
OPENAI_API_KEY=sk-...
OPENAI_MODEL=gpt-4o-mini
```

Then clear the config cache:

```bash
php artisan config:clear
```

## Demo Products

The `ProductSeeder` creates 50 demo smartphone products across brands such as iPhone, Samsung, Realme, OnePlus, Oppo, and Vivo. Examples:

- `iPhone 15`
- `iPhone 15 Pro`
- `Galaxy S24`
- `OnePlus 12`

You can ask the chatbox:

- "Is iPhone 15 in stock?"
- "Quantity of Galaxy S24?"
- "Which iPhone models are in stock?"
- "What brands do you have?"
- "Show me Samsung models"