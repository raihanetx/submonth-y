from playwright.sync_api import sync_playwright, expect

def run(playwright):
    browser = playwright.chromium.launch(headless=True)
    context = browser.new_context()
    page = context.new_page()

    # Verify frontend checkout page
    page.goto("http://localhost:8000/index.php?path=checkout")
    page.screenshot(path="jules-scratch/verification/checkout_page.png")

    # Log in to admin
    page.goto("http://localhost:8000/login.php")
    page.fill("input[name='username']", "admin")
    page.fill("input[name='password']", "admin")
    page.click("button[type='submit']")

    # Verify admin payment settings page
    page.goto("http://localhost:8000/admin.php?view=settings")
    page.screenshot(path="jules-scratch/verification/admin_settings_page.png")

    browser.close()

with sync_playwright() as playwright:
    run(playwright)