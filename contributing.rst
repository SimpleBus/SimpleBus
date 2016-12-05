Contributing
============

The documentation
-----------------

We are happy for documentation contributions. This section will show you how to get up and running with contribution the SimpleBus documentations. The documentation is formatted in ``reStructuredText``.
For this we use `Sphinx <http://www.sphinx-doc.org>`__,  a tool that makes it easy to create beautiful documentation. Assuming you have `Python <https://www.python.org>`__ already installed, install Sphinx:

.. code-block:: bash

    $ pip install sphinx sphinx-autobuild

Download GIT repository
^^^^^^^^^^^^^^^^^^^^^^^

Before you can start contributing the documentations you have to, fork the repository, clone it and create a new branch with the following commands:

.. code-block:: bash

    $ git clone https://github.com/your-name/repo-name.git
    $ git checkout -b documentation-description

After cloning the documentation repository you can open these files in your preferred IDE. Now it's time to start editing one of the the ``.rst`` files. For example the ``contributing.rst`` and add the information you are missing in the project.

Install the dependencies
^^^^^^^^^^^^^^^^^^^^^^^^

This documentation is making use of external open source dependencies. You can think of the Read the Docs theme and the Sphinx Extensions for PHP and Symfony. You can install these by the following command.

.. code-block:: bash

    $ pip install -r requirements.txt

Building the documentation
^^^^^^^^^^^^^^^^^^^^^^^^^^

After you have installed the open source dependencies and changed some files, you can manually rebuild the documentation HTML output. You can see the result by opening the ``_build/html/index.html`` file.

.. code-block:: bash

    $ make html

.. note:: You can use ``sphinx-autobuild`` to auto-reload your docs. Run ``make autobuild`` instead of ``make html``.

Spelling
^^^^^^^^

This documentation makes use of the Sphinx spelling extension, a spelling checker for Sphinx-based documentation. You run this by the following command:

.. code-block:: bash

    $ make spelling

If there are some technical words that are not recognized, then you have to add them to ``spelling_word_list.txt``. Please fill in this glossary in alphabetical order. As an example, you'll see the output below for the word ``symfony`` that's not found in the ``contributing.rst`` file.

.. code-block:: bash

    contributing.rst:55:symfony:

Commit & pull request
^^^^^^^^^^^^^^^^^^^^^

Now it's time to commit your changes and push it to your repository. The last step to finish your contribution, is to create an `pull requests <https://help.github.com/articles/about-pull-requests/>`__ for your valuable contribution. Thank you!
