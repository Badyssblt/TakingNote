class api {
  /**
   * @param {string} url
   */
  static get = async (url) => {
    const response = await fetch(url);

    return await response.json();
  };
}

export default api;
