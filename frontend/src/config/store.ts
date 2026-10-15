/**
 * Centralized Store Configuration for MocYy
 * Defines single source of truth for brand identity, official contacts, and WhatsApp.
 */
export const STORE_CONFIG = {
  name: "MocYy",
  fullName: "MocYy",
  tagline: "Buket Bunga & Hadiah Eksklusif",
  phoneDisplay: "+62 813-5487-734",
  whatsappNumber: "628135487734",
  whatsappUrl: "https://wa.me/628135487734",
  email: "halo@mocyy.com",
  instagram: "@mocyy.official",
  address: "Jl. Senopati Indah No. 88, Kebayoran Baru, Jakarta Selatan 12190",
  workingHours: "Senin - Minggu: 08.00 - 21.00 WIB",

  /**
   * Generates a context-aware WhatsApp direct link
   */
  getWhatsAppLink(message?: string): string {
    const base = `https://wa.me/${this.whatsappNumber}`;
    return message ? `${base}?text=${encodeURIComponent(message)}` : base;
  },
};
