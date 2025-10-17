from playwright.sync_api import sync_playwright

def run(playwright):
    browser = playwright.chromium.launch(headless=True)
    context = browser.new_context()
    page = context.new_page()

    page.goto("http://localhost:8000/index.php?path=checkout")

    # Click the first payment method button
    payment_buttons = page.locator("button[class*='border-gray-300']")
    first_button = payment_buttons.first
    first_button.click()

    page.screenshot(path="jules-scratch/verification/checkout_page_border_color.png")

    browser.close()

with sync_playwright() as playwright:
    run(playwright)