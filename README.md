# Railway Backend Setup

## Environment Variables Required

Set this in your Railway service:

\`\`\`
MYSQL_PUBLIC_URL=mysql://username:password@host:port/database_name
\`\`\`

## Database Setup

1. **Connect to Railway MySQL** using MySQL Workbench with your MYSQL_PUBLIC_URL
2. **Create your database** (e.g., `blog_indonesia`)
3. **Run the schema** from `database/schema-railway.sql`

## Deployment Steps

1. **Create Railway Service**
   - Connect your GitHub repository
   - Select the `railway-backend` folder as root
   - Set environment variable: `MYSQL_PUBLIC_URL`

2. **Deploy**
   - Railway will automatically build using the Dockerfile
   - Check logs for any errors

3. **Test**
   - Visit your Railway URL to see the backend status page
   - Test API endpoints: `/api/articles.php`, `/api/categories.php`

## File Structure

\`\`\`
railway-backend/
├── api/                 # API endpoints
├── admin/              # Admin panel
├── config/             # Database configuration
├── database/           # SQL schema files
├── Dockerfile          # Docker configuration
├── railway.json        # Railway configuration
├── .htaccess          # Apache configuration
├── index.php          # Backend status page
└── test-connection.php # Database test
\`\`\`

## Testing

- **Backend Status**: `https://your-railway-url.up.railway.app/`
- **Database Test**: `https://your-railway-url.up.railway.app/test-connection.php`
- **Admin Panel**: `https://your-railway-url.up.railway.app/admin/login.html`
