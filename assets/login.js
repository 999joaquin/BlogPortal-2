// Admin Login JavaScript
class AdminLogin {
  constructor() {
    // Fix API path for admin folder
    this.apiUrl = "../api"
    this.init()
  }

  init() {
    this.setupEventListeners()
    this.checkExistingSession()
  }

  setupEventListeners() {
    const loginForm = document.getElementById("login-form")
    if (loginForm) {
      loginForm.addEventListener("submit", (e) => {
        e.preventDefault()
        console.log("Form submitted - preventing default")
        this.handleLogin()
      })
    } else {
      console.error("Login form not found!")
    }
  }

  async checkExistingSession() {
    try {
      console.log("Checking existing session...")
      const response = await fetch(`${this.apiUrl}/auth.php`)
      if (response.ok) {
        const data = await response.json()
        console.log("Session check response:", data)
        if (data.authenticated) {
          // User is already logged in
          localStorage.setItem("admin_logged_in", "true")
          localStorage.setItem("admin_username", data.user.username)
          localStorage.setItem("admin_user", JSON.stringify(data.user))
          window.location.href = "dashboard.html"
        }
      }
    } catch (error) {
      console.log("No existing session:", error)
    }
  }

  async handleLogin() {
    console.log("handleLogin called")
    const username = document.getElementById("username").value
    const password = document.getElementById("password").value
    const alertContainer = document.getElementById("alert-container")

    console.log("Login attempt:", { username, password: "***" })

    // Clear previous alerts
    alertContainer.innerHTML = ""

    // Basic validation
    if (!username || !password) {
      this.showAlert("Username dan password harus diisi!", "danger")
      return
    }

    try {
      console.log("Making API request to:", `${this.apiUrl}/auth.php`)

      const response = await fetch(`${this.apiUrl}/auth.php`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          username: username,
          password: password,
        }),
      })

      console.log("Response status:", response.status)
      console.log("Response headers:", response.headers)

      if (response.ok) {
        const data = await response.json()
        console.log("Login response:", data)

        if (data.success) {
          // Store login state
          localStorage.setItem("admin_logged_in", "true")
          localStorage.setItem("admin_username", data.user.username)
          localStorage.setItem("admin_user", JSON.stringify(data.user))

          this.showAlert("Login berhasil! Mengalihkan...", "success")

          // Redirect to dashboard
          setTimeout(() => {
            window.location.href = "dashboard.html"
          }, 1500)
        } else {
          this.showAlert("Username atau password salah!", "danger")
        }
      } else {
        const error = await response.json()
        console.error("Login error response:", error)
        this.showAlert(error.error || "Login gagal!", "danger")
      }
    } catch (error) {
      console.error("Login error:", error)
      this.showAlert("Terjadi kesalahan saat login. Silakan coba lagi.", "danger")
    }
  }

  showAlert(message, type) {
    const alertContainer = document.getElementById("alert-container")
    const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `
    alertContainer.innerHTML = alertHtml
  }
}

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
  console.log("DOM loaded, initializing AdminLogin")
  new AdminLogin()
})
