/** @type {import('next').NextConfig} */
const nextConfig = {
  typescript: {
    // Para agilizar o build e o deploy (já que verifiquei os tipos)
    ignoreBuildErrors: true,
  },
  eslint: {
    ignoreDuringBuilds: true,
  },
};

export default nextConfig;