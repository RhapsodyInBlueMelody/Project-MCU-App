function appointmentForm() {
  return {
    packages: window.packagesData || [],
    doctors: window.doctorsData || [],
    selectedPackage: "",
    selectedDoctor: "",
    selectedSpecialization: "",
    searchTerm: "",
    specializations: [],
    init() {
      // Build unique specializations from doctors and packages
      const specs = {};
      this.doctors.forEach((doc) => {
        if (doc.id_spesialisasi && doc.nama_spesialisasi) {
          specs[doc.id_spesialisasi] = doc.nama_spesialisasi;
        }
      });
      this.packages.forEach((pkg) => {
        if (pkg.id_spesialisasi && pkg.keahlian_dibutuhkan_text) {
          specs[pkg.id_spesialisasi] = pkg.keahlian_dibutuhkan_text;
        }
      });
      this.specializations = Object.keys(specs).map((id) => ({
        id: id,
        name: specs[id],
      }));
    },
    get filteredPackages() {
      if (!this.selectedSpecialization) return this.packages;
      return this.packages.filter(
        (p) => p.id_spesialisasi === this.selectedSpecialization,
      );
    },
    get selectedPackageObj() {
      return this.packages.find((p) => p.id_paket == this.selectedPackage);
    },
    get filteredDoctors() {
      let filtered = this.doctors;
      if (this.selectedPackageObj && this.selectedPackageObj.id_spesialisasi) {
        filtered = filtered.filter(
          (doc) =>
            doc.id_spesialisasi == this.selectedPackageObj.id_spesialisasi,
        );
      }
      if (this.selectedSpecialization) {
        filtered = filtered.filter(
          (doc) => doc.id_spesialisasi == this.selectedSpecialization,
        );
      }
      if (this.searchTerm) {
        filtered = filtered.filter(
          (doc) =>
            doc.nama_dokter
              .toLowerCase()
              .includes(this.searchTerm.toLowerCase()) ||
            (doc.nama_spesialisasi &&
              doc.nama_spesialisasi
                .toLowerCase()
                .includes(this.searchTerm.toLowerCase())),
        );
      }
      return filtered;
    },
    updateDoctorsByPackage() {
      // When a package is chosen, auto-select related specialization if possible
      const pkg = this.selectedPackageObj;
      if (pkg && pkg.id_spesialisasi) {
        this.selectedSpecialization = pkg.id_spesialisasi;
      }
    },
    resetSpecialization() {
      this.selectedSpecialization = "";
    },
    formatCurrency(val) {
      if (!val) return "0";
      return parseInt(val).toLocaleString("id-ID");
    },
  };
}
