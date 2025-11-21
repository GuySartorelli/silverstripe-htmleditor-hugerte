/* global jest, describe, afterEach, it, expect */

import HugeRTEActionRegistrar from '../HugeRTEActionRegistrar';

jest.unmock('../HugeRTEActionRegistrar.js');

describe('HugeRTEActionRegistrar', () => {
  describe('addCommandWithUrlTest method', () => {
    it('should return the default command when there\'s registered command', () => {
      const cmd = HugeRTEActionRegistrar.getEditorCommandFromUrl('http://googl.come/');
      expect(cmd).toBe('sslinkexternal');
    });

    it('should return the default command when there\'s no matched command', () => {
      HugeRTEActionRegistrar.addCommandWithUrlTest('sslinkemail', /^mailto:/);
      const cmd = HugeRTEActionRegistrar.getEditorCommandFromUrl('[sitetree_link,id=2]');
      expect(cmd).toBe('sslinkexternal');
    });

    it('should a matched command when there is one', () => {
      HugeRTEActionRegistrar.addCommandWithUrlTest('sslinkemail', /^mailto:/);
      HugeRTEActionRegistrar.addCommandWithUrlTest('sslinkfile', /^\[sitetree_link/);

      const cmdFile = HugeRTEActionRegistrar.getEditorCommandFromUrl('[sitetree_link,id=2]');
      expect(cmdFile).toBe('sslinkfile');

      const cmdEmail = HugeRTEActionRegistrar.getEditorCommandFromUrl('mailto:john.doe@test.com');
      expect(cmdEmail).toBe('sslinkemail');

      const cmdExt = HugeRTEActionRegistrar.getEditorCommandFromUrl('http://googl.come/');
      expect(cmdExt).toBe('sslinkexternal');
    });
  });

  describe('getSortedActions() should return sorted actions', () => {
    HugeRTEActionRegistrar.actions = {};
    HugeRTEActionRegistrar.addAction('menuTest', { text: 'Apple', priority: 10 });
    HugeRTEActionRegistrar.addAction('menuTest', { text: 'Two', priority: 50 });
    HugeRTEActionRegistrar.addAction('menuTest', { text: 'One', priority: 50 });
    HugeRTEActionRegistrar.addAction('menuTest', { text: 'Five', priority: 51 });

    const sortedActions = HugeRTEActionRegistrar.getSortedActions('menuTest');
    const output = sortedActions.map(item => item.text);
    expect(output).toEqual(['Five', 'One', 'Two', 'Apple']);
  });

  describe('getSortedActions() should allow filtering of global actions', () => {
    HugeRTEActionRegistrar.actions = {};
    HugeRTEActionRegistrar.addAction('menuTest', { text: 'Global1', priority: 10 });
    HugeRTEActionRegistrar.addAction('menuTest', { text: 'Global2', priority: 50 });
    HugeRTEActionRegistrar.addAction('menuTest', { text: 'Local1', priority: 100 }, 'special');
    HugeRTEActionRegistrar.addAction('menuTest', { text: 'Local2', priority: 150 }, 'special');

    let sortedActions;
    let output;
    // No config ID gets only global actions
    sortedActions = HugeRTEActionRegistrar.getSortedActions('menuTest');
    output = sortedActions.map(item => item.text);
    expect(output).toEqual(['Global2', 'Global1']);

    // With config id and includeGlobal=true gets everything
    sortedActions = HugeRTEActionRegistrar.getSortedActions('menuTest', 'special');
    output = sortedActions.map(item => item.text);
    expect(output).toEqual(['Local2', 'Local1', 'Global2', 'Global1']);

    // With config id and includeGlobal=false gets only special
    sortedActions = HugeRTEActionRegistrar.getSortedActions('menuTest', 'special', false);
    output = sortedActions.map(item => item.text);
    expect(output).toEqual(['Local2', 'Local1']);
  });
});
