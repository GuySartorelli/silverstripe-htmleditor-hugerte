import Injector from 'lib/Injector';
import HugeRteHtmlEditorField from 'components/HugeRteHtmlEditorField/HugeRteHtmlEditorField';

export default () => {
  Injector.component.registerMany({
    HugeRteHtmlEditorField,
  });
};
